function initializeLayoutInteractions() {
    const navbar = document.getElementById('navbar');
    const scrollToTop = document.getElementById('scrollToTop');
    const menu = document.getElementById('userDropdown');
    const trigger = document.querySelector('.user-menu-trigger');
    const mobileTrigger = document.querySelector('.nav-mobile-trigger');
    const mobileMenu = document.getElementById('mobileNav');

    const syncScroll = () => {
        navbar?.classList.toggle('scrolled', window.scrollY > 50);
        scrollToTop?.classList.toggle('visible', window.scrollY > 300);
    };
    window.addEventListener('scroll', syncScroll, { passive: true });
    syncScroll();

    scrollToTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    const closeMenu = () => {
        menu?.classList.remove('show');
        trigger?.setAttribute('aria-expanded', 'false');
    };
    const closeMobileMenu = () => {
        mobileMenu?.classList.remove('show');
        mobileTrigger?.setAttribute('aria-expanded', 'false');
    };
    mobileTrigger?.addEventListener('click', event => {
        event.stopPropagation();
        closeMenu();
        const open = mobileMenu.classList.toggle('show');
        mobileTrigger.setAttribute('aria-expanded', String(open));
    });
    trigger?.addEventListener('click', event => {
        event.stopPropagation();
        closeMobileMenu();
        const open = menu.classList.toggle('show');
        trigger.setAttribute('aria-expanded', String(open));
    });
    document.addEventListener('click', event => {
        if (menu && !event.target.closest('.user-menu')) closeMenu();
        if (mobileMenu && !event.target.closest('.nav-mobile-menu')) closeMobileMenu();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && mobileMenu?.classList.contains('show')) {
            closeMobileMenu();
            mobileTrigger.focus();
        }
        if (event.key === 'Escape' && menu?.classList.contains('show')) {
            closeMenu();
            trigger.focus();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLayoutInteractions, { once: true });
} else {
    initializeLayoutInteractions();
}
