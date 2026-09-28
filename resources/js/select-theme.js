// Use a styled option panel on precise pointers while keeping native selects
// as the source of truth for forms, accessibility, and touch devices.
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
let current = null;
let panel = null;
let activeIndex = -1;

function closePanel() {
    panel?.remove();
    panel = null;
    if (current) current.removeAttribute('aria-expanded');
    current = null;
    activeIndex = -1;
}

function setActive(index) {
    if (!panel) return;
    const options = [...panel.querySelectorAll('.app-select-option')];
    if (!options.length) return;
    activeIndex = Math.max(0, Math.min(index, options.length - 1));
    options.forEach((option, i) => option.dataset.active = String(i === activeIndex));
    options[activeIndex].scrollIntoView({ block: 'nearest' });
}

function choose(index) {
    const select = current;
    if (!select || select.options[index]?.disabled) return;
    const changed = select.selectedIndex !== index;
    select.selectedIndex = index;
    closePanel();
    select.focus({ preventScroll: true });
    if (changed) {
        select.dispatchEvent(new Event('input', { bubbles: true }));
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function openPanel(select) {
    if (select.disabled || select.hidden || !select.getClientRects().length) return;
    if (current === select) { closePanel(); return; }
    closePanel();
    current = select;
    select.focus({ preventScroll: true });
    select.setAttribute('aria-expanded', 'true');

    panel = document.createElement('div');
    panel.className = 'app-select-options';
    panel.setAttribute('role', 'listbox');
    panel.setAttribute('aria-label', select.labels?.[0]?.textContent.trim() || select.getAttribute('aria-label') || select.name || 'Options');
    [...select.options].forEach((option, index) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'app-select-option';
        item.textContent = option.textContent.trim();
        item.disabled = option.disabled;
        item.setAttribute('role', 'option');
        item.setAttribute('aria-selected', String(index === select.selectedIndex));
        item.addEventListener('click', () => choose(index));
        panel.append(item);
    });
    document.body.append(panel);

    const rect = select.getBoundingClientRect();
    const width = Math.max(rect.width, 170);
    panel.style.width = `${Math.min(width, window.innerWidth - 16)}px`;
    panel.style.left = `${Math.max(8, Math.min(rect.left, window.innerWidth - panel.offsetWidth - 8))}px`;
    panel.style.top = `${rect.bottom + 6}px`;
    if (panel.getBoundingClientRect().bottom > window.innerHeight - 8 && rect.top > window.innerHeight - rect.bottom) {
        panel.style.top = `${Math.max(8, rect.top - panel.offsetHeight - 6)}px`;
    }
    setActive(Math.max(0, select.selectedIndex));
}

function eligible(select) {
    return select instanceof HTMLSelectElement && !select.multiple && !select.hasAttribute('size');
}

document.addEventListener('mousedown', event => {
    if (!finePointer.matches) return;
    const select = event.target.closest?.('select');
    if (eligible(select)) {
        event.preventDefault();
    }
}, true);

document.addEventListener('click', event => {
    if (finePointer.matches && eligible(event.target)) {
        event.preventDefault();
        openPanel(event.target);
    } else if (panel && !panel.contains(event.target)) {
        closePanel();
    }
}, true);

document.addEventListener('keydown', event => {
    if (!finePointer.matches) return;
    const select = event.target;
    if (!eligible(select)) {
        if (event.key === 'Escape') closePanel();
        return;
    }
    if (event.key === 'Escape' && current === select) {
        event.preventDefault();
        closePanel();
    } else if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        if (current === select) choose(activeIndex);
        else openPanel(select);
    } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        if (current !== select) openPanel(select);
        else setActive(activeIndex + (event.key === 'ArrowDown' ? 1 : -1));
    }
}, true);

window.addEventListener('resize', closePanel);
