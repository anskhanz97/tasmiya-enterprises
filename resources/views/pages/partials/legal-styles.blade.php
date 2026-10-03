<style>
    .legal-page { background: #f8f6fc; color: #302853; min-height: 80vh; }
    .legal-page a { text-underline-offset: 3px; }
    .legal-hero { position: relative; min-height: 455px; display: flex; align-items: end; overflow: hidden; background: #241d52; color: #fff; }
    .legal-hero--privacy { min-height: 0; aspect-ratio: 1672 / 941; align-items: center; }
    .legal-hero > img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .legal-hero__shade { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(29,21,71,.92) 0%, rgba(39,28,83,.73) 39%, rgba(54,40,97,.10) 84%), linear-gradient(0deg, rgba(36,25,77,.24), transparent 55%); }
    .legal-hero__inner { position: relative; z-index: 1; width: min(1160px, calc(100% - 48px)); margin: 0 auto; padding: 52px 0 48px; }
    .legal-back { display: inline-block; color: #e6ddfa; font-size: .84rem; font-weight: 600; margin-bottom: 35px; text-decoration: none; }
    .legal-back:hover { color: #fff; text-decoration: underline; }
    .legal-hero h1 { max-width: 660px; margin: 0; color: #fff; font-size: clamp(2.6rem, 5.5vw, 4.8rem); line-height: 1.08; letter-spacing: -.055em; font-weight: 800; }
    .legal-hero p { max-width: 550px; margin: 15px 0 26px; color: #f0eafa; font-size: clamp(1rem, 1.8vw, 1.2rem); line-height: 1.6; }
    .legal-updated { display: inline-block; border: 1px solid rgba(255,255,255,.5); border-radius: 50px; padding: 7px 13px; color: #fff; font-size: .76rem; font-weight: 600; }
    .legal-layout { width: min(1160px, calc(100% - 48px)); margin: 0 auto; padding: 64px 0 92px; display: grid; grid-template-columns: 210px minmax(0, 760px); gap: clamp(44px, 8vw, 112px); align-items: start; }
    .legal-index { position: sticky; top: 105px; display: flex; flex-direction: column; gap: 15px; padding: 7px 0 0 18px; border-left: 2px solid #d9d1ec; }
    .legal-index__title { font-size: .77rem; font-weight: 700; color: #513e94; margin-bottom: 5px; }
    .legal-index a { text-decoration: none; color: #726987; font-size: .83rem; line-height: 1.5; }
    .legal-index a:hover { color: #6d50bc; text-decoration: underline; }
    .legal-copy { max-width: 760px; }
    .legal-intro { margin: 0 0 42px; padding: 0 0 34px; border-bottom: 1px solid #ded8eb; color: #392d66; font-size: clamp(1.08rem, 1.8vw, 1.3rem); line-height: 1.75; font-weight: 500; }
    .legal-section { padding: 0 0 27px; margin: 0 0 28px; border-bottom: 1px solid #e5e0ee; scroll-margin-top: 110px; }
    .legal-section--last { border-bottom: 0; }
    .legal-section h2 { color: #2c2259; font-size: clamp(1.25rem, 2vw, 1.62rem); line-height: 1.3; letter-spacing: -.03em; font-weight: 700; margin: 0 0 13px; }
    .legal-section p { color: #615b70; font-size: .98rem; line-height: 1.9; margin: 0; }
    .legal-section a { color: #6848ae; font-weight: 600; }
    .legal-next { display: flex; justify-content: space-between; align-items: center; gap: 20px; padding: 23px 26px; background: #eee9f8; border: 1px solid #ded3f2; border-radius: 14px; }
    .legal-next > span { color: #706781; font-size: .82rem; }
    .legal-next a { color: #56359d; font-weight: 700; font-size: .9rem; text-decoration: none; }
    .legal-next a:hover { text-decoration: underline; }
    .legal-next a span { margin-left: 5px; }
    .legal-page a:focus-visible { outline: 3px solid #8b5cf6; outline-offset: 4px; border-radius: 3px; }
    @media (max-width: 750px) {
        .legal-hero { min-height: 400px; }
        .legal-hero > img { object-position: 68% center; }
        .legal-hero__shade { background: linear-gradient(90deg, rgba(29,21,71,.93), rgba(49,33,91,.65)); }
        .legal-hero__inner, .legal-layout { width: min(100% - 36px, 620px); }
        .legal-hero__inner { padding: 40px 0; }
        .legal-hero--privacy { min-height: 0; aspect-ratio: auto; display: flex; flex-direction: column; align-items: stretch; }
        .legal-hero--privacy > img { position: relative; order: 2; height: auto; width: 100%; object-fit: contain; object-position: center; }
        .legal-hero--privacy .legal-hero__inner { order: 1; }
        .legal-hero--privacy .legal-hero__shade { display: none; }
        .legal-layout { display: block; padding: 42px 0 68px; }
        .legal-index { position: static; flex-direction: row; flex-wrap: wrap; gap: 9px 15px; padding: 0 0 24px; margin-bottom: 32px; border-left: 0; border-bottom: 1px solid #ded8eb; }
        .legal-index__title { width: 100%; }
        .legal-index a { font-size: .78rem; }
        .legal-intro { margin-bottom: 32px; }
        .legal-next { align-items: flex-start; flex-direction: column; }
    }
</style>
