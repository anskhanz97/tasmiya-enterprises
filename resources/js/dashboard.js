import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    gsap.from('.dw-hero-copy > *', { opacity: 0, y: 26, duration: 0.75, stagger: 0.09, ease: 'power2.out' });
    gsap.from('.dw-portrait', { opacity: 0, x: 42, scale: 0.94, duration: 1.15, ease: 'power2.out' });
    gsap.utils.toArray('[data-dw-card]').forEach((card) => {
        gsap.from(card, { opacity: 0, y: 30, duration: 0.65, ease: 'power2.out', scrollTrigger: { trigger: card, start: 'top 92%', once: true } });
    });
}
