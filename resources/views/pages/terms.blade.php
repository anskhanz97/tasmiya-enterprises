@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
@include('pages.partials.legal-styles')
@php($companyEmail = \App\Models\SiteSetting::get('integration_company_email') ?: 'contact@tasmiya.com')

<main class="legal-page">
    <header class="legal-hero legal-hero--terms">
        <img src="{{ asset('images/legal/terms-consultation.webp') }}" alt="Professionals reviewing a business proposal together at a desk" fetchpriority="high" width="1672" height="941">
        <div class="legal-hero__shade"></div>
        <div class="legal-hero__inner">
            <a class="legal-back" href="{{ route('home') }}">← Back to Tasmiya</a>
            <h1>Terms of Service</h1>
            <p>A clear starting point for using our website and working with our team.</p>
            <span class="legal-updated">Updated 28 September 2026</span>
        </div>
    </header>

    <div class="legal-layout">
        <nav class="legal-index" aria-label="On this page">
            <span class="legal-index__title">On this page</span>
            <a href="#terms-services">Our services</a>
            <a href="#terms-accounts">Accounts and use</a>
            <a href="#terms-payments">Fees and payments</a>
            <a href="#terms-materials">Materials and tools</a>
            <a href="#terms-contact">Questions</a>
        </nav>

        <article class="legal-copy">
            <p class="legal-intro">These terms apply to Tasmiya Enterprises' website and portal. By using them, you agree to use them lawfully and respectfully. Specific project terms, deliverables, timelines, and prices are agreed separately in a proposal, invoice, or other written arrangement.</p>

            <section id="terms-services" class="legal-section">
                <h2>Our services</h2>
                <p>We offer taxation, technology, and technical support services. Descriptions on this website are general information, not a promise of a particular outcome. We will explain the scope of work for your project before it begins.</p>
            </section>

            <section id="terms-accounts" class="legal-section">
                <h2>Accounts and responsible use</h2>
                <p>Portal access is by invitation. Keep your sign-in details private, provide accurate information, and tell us promptly if you suspect unauthorized access. Do not misuse the site, upload harmful material, or attempt to access information that is not yours. We may restrict access when needed to protect the portal or other users.</p>
            </section>

            <section id="terms-payments" class="legal-section">
                <h2>Fees and payments</h2>
                <p>Fees, due dates, and any refund or cancellation arrangements should be confirmed for the particular service. Where offered, you may pay through the methods shown in the portal. A bank or wallet transfer is not confirmed until our team verifies the payment; submitting a reference or receipt alone does not mark it paid.</p>
            </section>

            <section id="terms-materials" class="legal-section">
                <h2>Materials and third-party tools</h2>
                <p>You remain responsible for the accuracy of information and files you provide and for your rights to share them. Our website content and branding belong to Tasmiya Enterprises unless stated otherwise. Optional tools such as Google Drive, OneDrive, and payment providers are also subject to their own terms and availability.</p>
            </section>

            <section id="terms-contact" class="legal-section legal-section--last">
                <h2>Changes and questions</h2>
                <p>We may update this page when our website or services change, with the latest date shown above. For questions about these terms or a particular engagement, email <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>. Our <a href="{{ route('privacy') }}">Privacy Policy</a> explains how we handle personal information.</p>
            </section>

            <div class="legal-next"><span>Also worth reading</span><a href="{{ route('privacy') }}">Privacy Policy <span aria-hidden="true">→</span></a></div>
        </article>
    </div>
</main>
@endsection
