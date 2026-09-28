@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
@include('pages.partials.legal-styles')
@php($companyEmail = \App\Models\SiteSetting::get('integration_company_email') ?: 'contact@tasmiya.com')

<main class="legal-page">
    <header class="legal-hero legal-hero--privacy">
        <img src="{{ asset('images/legal/privacy-office.webp') }}" alt="A quiet consultancy workspace with a laptop and neatly arranged documents" fetchpriority="high" width="1672" height="941">
        <div class="legal-hero__shade"></div>
        <div class="legal-hero__inner">
            <a class="legal-back" href="{{ route('home') }}">← Back to Tasmiya</a>
            <h1>Privacy Policy</h1>
            <p>What we collect, why we need it, and how you can reach us about your information.</p>
            <span class="legal-updated">Updated 28 September 2026</span>
        </div>
    </header>

    <div class="legal-layout">
        <nav class="legal-index" aria-label="On this page">
            <span class="legal-index__title">On this page</span>
            <a href="#privacy-information">Information we collect</a>
            <a href="#privacy-use">How we use it</a>
            <a href="#privacy-sharing">Sharing and security</a>
            <a href="#privacy-choices">Your choices</a>
            <a href="#privacy-contact">Contact us</a>
        </nav>

        <article class="legal-copy">
            <p class="legal-intro">Tasmiya Enterprises provides taxation, technology, and technical support services. This page explains how we handle information when you browse our website, send an inquiry, use an invited account, or arrange a service or payment.</p>

            <section id="privacy-information" class="legal-section">
                <h2>Information we collect</h2>
                <p>We receive the details you choose to give us, such as your name, email, phone number, company details, inquiry, account profile, and files relevant to a requested service. If you make a payment, we may keep the amount, method, reference, and proof you submit. We also use essential session and security cookies and may record basic technical information needed to run and protect the website.</p>
            </section>

            <section id="privacy-use" class="legal-section">
                <h2>How we use it</h2>
                <p>We use this information to answer inquiries, deliver and support our services, manage accounts, verify payments, maintain records, and keep the portal secure. We keep it only as long as reasonably needed for those purposes or applicable record-keeping requirements.</p>
            </section>

            <section id="privacy-sharing" class="legal-section">
                <h2>Sharing and security</h2>
                <p>We do not sell your personal information. We may share only what is needed with people working on your request and with providers that support our website, email, messaging, cloud-image import, or payments, where those features are used. If you choose Google Drive or OneDrive, the relevant provider handles your sign-in under its own terms. We use access controls and reasonable safeguards, although no online system is entirely risk-free.</p>
            </section>

            <section id="privacy-choices" class="legal-section">
                <h2>Your choices</h2>
                <p>You can ask us to review or correct information you have provided, or request its deletion where we no longer need to keep it. You can manage cookies in your browser, though essential cookies are needed for sign-in and some portal features.</p>
            </section>

            <section id="privacy-contact" class="legal-section legal-section--last">
                <h2>Questions about your information?</h2>
                <p>Email <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>. We may update this policy as our services change; the date above shows the latest version.</p>
            </section>

            <div class="legal-next"><span>Also worth reading</span><a href="{{ route('terms') }}">Terms of Service <span aria-hidden="true">→</span></a></div>
        </article>
    </div>
</main>
@endsection
