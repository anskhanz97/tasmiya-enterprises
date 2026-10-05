(() => {
    window.TasmiyaPager = (root, {count, onChange, wrap = false}) => {
        let current = 0;
        const previous = root.querySelector('[data-page-prev]'), next = root.querySelector('[data-page-next]'), dots = root.querySelector('[data-page-dots]');
        const total = () => Math.max(1, typeof count === 'function' ? count() : count);
        function set(index, notify = true) {
            const pages = total();
            current = wrap ? (index + pages) % pages : Math.max(0, Math.min(index, pages - 1));
            previous.disabled = !wrap && current === 0; next.disabled = !wrap && current === pages - 1;
            dots.replaceChildren();
            for (let page = 0; page < pages; page++) {
                const dot = document.createElement('button'); dot.type = 'button';
                dot.setAttribute('aria-label', `Show page ${page + 1} of ${pages}`); dot.setAttribute('aria-current', String(page === current));
                dot.addEventListener('click', () => set(page)); dots.appendChild(dot);
            }
            if (notify) onChange(current);
        }
        previous.addEventListener('click', () => set(current - 1)); next.addEventListener('click', () => set(current + 1)); set(0);
        return {set, current: () => current};
    };
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver(entries => entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            observer.unobserve(entry.target);
            const element = entry.target, target = Number(element.dataset.themeCounter);
            if (!Number.isFinite(target) || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            let start; element.textContent = '0';
            const tick = time => { start ??= time; const progress = Math.min((time-start)/2000,1); element.textContent = String(Math.floor(target*(1-(1-progress)**3))); if(progress<1) requestAnimationFrame(tick); };
            requestAnimationFrame(tick);
        }), {threshold:.2});
        document.querySelectorAll('[data-theme-counter]').forEach(element => observer.observe(element));
        document.querySelectorAll('[data-theme-page-size]').forEach(section => {
            const items = [...section.querySelectorAll('[data-page-item]')], size = Number(section.dataset.themePageSize), pager = section.querySelector('.theme-pager');
            if (!pager || items.length <= size) return; pager.hidden = false;
            TasmiyaPager(pager,{count:Math.ceil(items.length/size),onChange:page=>items.forEach((item,index)=>item.hidden=Math.floor(index/size)!==page)});
        });
    });
})();
