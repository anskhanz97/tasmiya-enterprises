@extends('layouts.app')

@section('title', 'Frequently Asked Questions')

@section('content')
<style>
    .faq-page { background: #f8f6fc; color: #302853; min-height: 80vh; }
    .faq-hero { position: relative; overflow: hidden; padding: clamp(104px, 10vw, 145px) 20px 95px; color: #fff; background: radial-gradient(circle at 83% 20%, rgba(184,151,252,.26), transparent 32%), linear-gradient(125deg, #211a50, #49348c 67%, #7755ac); }
    .faq-hero::after { content: ''; position: absolute; right: -110px; bottom: -290px; width: 510px; height: 510px; border: 1px solid rgba(255,255,255,.2); border-radius: 50%; box-shadow: 0 0 0 70px rgba(255,255,255,.035), 0 0 0 140px rgba(255,255,255,.025); pointer-events: none; }
    .faq-hero__inner { width: min(1120px, 100%); margin: 0 auto; }
    .faq-hero h1 { max-width: 820px; color: #fff; font-size: clamp(2.5rem, 5.5vw, 4.5rem); line-height: 1.1; letter-spacing: -.05em; font-weight: 800; margin-bottom: 18px; }
    .faq-hero p { max-width: 620px; color: #e9e2f8; font-size: 1.12rem; line-height: 1.7; margin: 0; }
    .faq-layout { width: min(1120px, calc(100% - 40px)); margin: 0 auto; padding: 70px 0 90px; display: grid; grid-template-columns: 230px minmax(0, 760px); gap: clamp(38px, 7vw, 90px); align-items: start; }
    .faq-jump { position: sticky; top: 105px; display: grid; gap: 14px; }
    .faq-jump span { color: #786e8d; font-size: .8rem; font-weight: 700; }
    .faq-jump a { color: #5b43a0; font-size: .92rem; text-decoration: none; font-weight: 600; }
    .faq-jump a:hover { text-decoration: underline; }
    .faq-group { margin-bottom: 48px; scroll-margin-top: 110px; }
    .faq-group h2 { font-size: clamp(1.45rem, 2.4vw, 1.9rem); color: #2c2259; letter-spacing: -.03em; margin: 0 0 22px; }
    .faq-item { border-top: 1px solid #ded8eb; }
    .faq-item:last-of-type { border-bottom: 1px solid #ded8eb; }
    .faq-item summary { list-style: none; cursor: pointer; display: flex; justify-content: space-between; gap: 20px; align-items: center; color: #352a64; font-size: 1rem; font-weight: 700; line-height: 1.5; padding: 23px 0; }
    .faq-item summary::-webkit-details-marker { display: none; }
    .faq-item summary::after { content: '+'; flex: 0 0 28px; width: 28px; height: 28px; display: grid; place-items: center; border-radius: 50%; background: #ebe4f8; color: #6241a6; font-size: 1.2rem; font-weight: 400; }
    .faq-item[open] summary::after { content: '−'; }
    .faq-item p { color: #625c70; line-height: 1.85; font-size: .96rem; padding: 0 52px 24px 0; margin: 0; }
    .faq-item a { color: #6848ae; font-weight: 600; }
    .faq-contact { background: #eee9f8; border: 1px solid #ded3f2; border-radius: 18px; padding: 28px 32px; }
    .faq-contact h2 { color: #30235e; font-size: 1.4rem; margin-bottom: 8px; }
    .faq-contact p { color: #675e7e; line-height: 1.7; margin-bottom: 18px; }
    .faq-contact a { display: inline-block; padding: 11px 19px; border-radius: 9px; background: #6245ad; color: #fff; text-decoration: none; font-size: .9rem; font-weight: 700; }
    .faq-contact a:hover { background: #49328a; }
    .faq-page a:focus-visible, .faq-page summary:focus-visible { outline: 3px solid #8b5cf6; outline-offset: 4px; }
    @media (max-width: 760px) { .faq-layout { display: block; padding: 42px 0 65px; } .faq-jump { position: static; display: flex; flex-wrap: wrap; gap: 10px 20px; border-bottom: 1px solid #ded8eb; padding-bottom: 26px; margin-bottom: 38px; } .faq-jump span { width: 100%; } .faq-item p { padding-right: 10px; } .faq-contact { padding: 24px; } }
</style>

<main class="faq-page">
    <header class="faq-hero"><div class="faq-hero__inner">
        <h1>Questions, answered clearly.</h1>
        <p>Here are the things clients ask most often about our services, the portal, and payments.</p>
    </div></header>

    <div class="faq-layout">
        <nav class="faq-jump" aria-label="FAQ topics">
            <span>Browse topics</span>
            <a href="#faq-services">Services and projects</a>
            <a href="#faq-portal">Accounts and the portal</a>
            <a href="#faq-payments">Payments</a>
        </nav>

        <div>
            <section class="faq-group" id="faq-services">
                <h2>Services and projects</h2>
                <details class="faq-item"><summary>What does Tasmiya Enterprises do?</summary><p>We work across taxation, IT and digital services, and technical support. Browse the <a href="{{ route('services.index') }}">services page</a> for current offerings, or tell us what you need and we will direct you to the right person.</p></details>
                <details class="faq-item"><summary>How do I start a project or ask for a quote?</summary><p>Send an inquiry through our <a href="{{ route('contact.create') }}">contact page</a>. A short description of your goals, timeline, and any relevant business details helps us understand the request. We will discuss scope and pricing with you before work begins.</p></details>
                <details class="faq-item"><summary>Can I speak directly with a specialist?</summary><p>Yes. Our <a href="{{ route('team.index') }}">team page</a> introduces the people behind each area of work. You can also use the contact options shown on a service page to reach the relevant specialist.</p></details>
            </section>

            <section class="faq-group" id="faq-portal">
                <h2>Accounts and the portal</h2>
                <details class="faq-item"><summary>Can anyone create a portal account?</summary><p>Accounts are arranged by our team rather than opened through public sign-up. If you need access, contact us and we will let you know the next steps.</p></details>
                <details class="faq-item"><summary>Do I need Google Drive or OneDrive?</summary><p>No. Cloud image pickers are optional conveniences for choosing a profile image. You can upload an image from your device instead. Connecting a cloud account is handled by that provider when you choose to use it.</p></details>
            </section>

            <section class="faq-group" id="faq-payments">
                <h2>Payments</h2>
                <details class="faq-item"><summary>Which payment methods can I use?</summary><p>The available choices are shown when you create a payment. Depending on what is enabled, they may include bank transfer, Raast, Easypaisa, JazzCash, or card checkout. Check the displayed payment instructions before sending money.</p></details>
                <details class="faq-item"><summary>When is a manual payment marked paid?</summary><p>After a transfer, submit the reference and any requested proof in the portal. Our team verifies incoming funds before marking it paid. A submitted receipt alone is not confirmation.</p></details>
                <details class="faq-item"><summary>What if I have a billing or service question?</summary><p>Contact us with your service name or payment reference. Please avoid sending card details or account passwords in a message.</p></details>
            </section>

            <div class="faq-contact"><h2>Still need a hand?</h2><p>Tell us what you are trying to do and our team will point you in the right direction.</p><a href="{{ route('contact.create') }}">Contact our team</a></div>
        </div>
    </div>
</main>
@endsection
