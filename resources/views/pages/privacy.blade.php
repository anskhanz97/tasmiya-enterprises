@extends('layouts.app')

@section('title', 'Privacy Policy - Tasmiya Enterprises')

@section('content')
<style>
    .policy-hero {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        padding: 100px 0 60px;
        text-align: center;
        color: white;
    }
    
    .policy-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }
    
    .policy-hero p {
        font-size: 1.1rem;
        opacity: 0.95;
    }
    
    .policy-content {
        max-width: 900px;
        margin: 60px auto;
        padding: 0 20px;
    }
    
    .policy-section {
        margin-bottom: 40px;
    }
    
    .policy-section h2 {
        font-size: 1.8rem;
        color: #1a202c;
        margin-bottom: 1rem;
        font-weight: 700;
        border-left: 4px solid #11998e;
        padding-left: 15px;
    }
    
    .policy-section h3 {
        font-size: 1.3rem;
        color: #2d3748;
        margin: 1.5rem 0 0.8rem;
        font-weight: 600;
    }
    
    .policy-section p, .policy-section li {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #4a5568;
        margin-bottom: 1rem;
    }
    
    .policy-section ul {
        padding-left: 30px;
        margin-bottom: 1.5rem;
    }
    
    .policy-section li {
        margin-bottom: 0.8rem;
    }
    
    .last-updated {
        background: #f7fafc;
        padding: 15px 20px;
        border-radius: 10px;
        border-left: 4px solid #11998e;
        margin-bottom: 40px;
        font-weight: 600;
        color: #2d3748;
    }
    
    .highlight-box {
        background: #e6fffa;
        border: 2px solid #11998e;
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
    }
    
    .highlight-box p {
        margin: 0;
        color: #234e52;
        font-weight: 500;
    }
</style>

<div class="policy-hero">
    <h1>Privacy Policy</h1>
    <p>Your privacy is important to us</p>
</div>

<div class="policy-content">
    <div class="last-updated">
        📅 Last Updated: February 11, 2026
    </div>
    
    <div class="policy-section">
        <h2>1. Introduction</h2>
        <p>
            Welcome to Tasmiya Enterprises. We respect your privacy and are committed to protecting your 
            personal data. This privacy policy explains how we collect, use, and safeguard your information 
            when you use our services or visit our website.
        </p>
        <div class="highlight-box">
            <p>
                🔒 <strong>Our Commitment:</strong> We will never sell your personal information to third parties. 
                Your data is used solely to provide and improve our services to you.
            </p>
        </div>
    </div>
    
    <div class="policy-section">
        <h2>2. Information We Collect</h2>
        <h3>2.1 Personal Information</h3>
        <p>We may collect the following types of personal information:</p>
        <ul>
            <li><strong>Contact Information:</strong> Name, email address, phone number, and mailing address</li>
            <li><strong>Account Information:</strong> Username, password, and profile details</li>
            <li><strong>Professional Information:</strong> Job title, company name, and professional qualifications</li>
            <li><strong>Payment Information:</strong> Billing address and payment card details (processed securely through third-party payment processors)</li>
        </ul>
        
        <h3>2.2 Automatically Collected Information</h3>
        <p>When you visit our website, we automatically collect:</p>
        <ul>
            <li>IP address and browser type</li>
            <li>Device information and operating system</li>
            <li>Pages visited and time spent on our site</li>
            <li>Referring website addresses</li>
        </ul>
    </div>
    
    <div class="policy-section">
        <h2>3. How We Use Your Information</h2>
        <p>We use your personal information for the following purposes:</p>
        <ul>
            <li>To provide and maintain our services</li>
            <li>To process your transactions and send transaction notifications</li>
            <li>To respond to your inquiries and provide customer support</li>
            <li>To send you updates, newsletters, and marketing communications (with your consent)</li>
            <li>To improve our website and services based on your feedback</li>
            <li>To detect, prevent, and address technical issues or fraudulent activity</li>
            <li>To comply with legal obligations and enforce our terms</li>
        </ul>
    </div>
    
    <div class="policy-section">
        <h2>4. Data Security</h2>
        <p>
            We implement appropriate technical and organizational security measures to protect your 
            personal information against unauthorized access, alteration, disclosure, or destruction. 
            This includes:
        </p>
        <ul>
            <li>Encryption of sensitive data in transit and at rest</li>
            <li>Regular security assessments and updates</li>
            <li>Restricted access to personal information on a need-to-know basis</li>
            <li>Secure authentication and access controls</li>
        </ul>
        <p>
            However, no method of transmission over the internet is 100% secure. While we strive to 
            protect your data, we cannot guarantee absolute security.
        </p>
    </div>
    
    <div class="policy-section">
        <h2>5. Data Sharing and Disclosure</h2>
        <p>We do not sell your personal information. We may share your data only in the following circumstances:</p>
        <ul>
            <li><strong>Service Providers:</strong> With trusted third-party vendors who assist in operating our business (e.g., payment processors, hosting providers)</li>
            <li><strong>Legal Requirements:</strong> When required by law or to protect our legal rights</li>
            <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
            <li><strong>With Your Consent:</strong> When you explicitly authorize us to share your information</li>
        </ul>
    </div>
    
    <div class="policy-section">
        <h2>6. Your Rights</h2>
        <p>You have the following rights regarding your personal data:</p>
        <ul>
            <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
            <li><strong>Correction:</strong> Request correction of inaccurate or incomplete data</li>
            <li><strong>Deletion:</strong> Request deletion of your personal information</li>
            <li><strong>Object:</strong> Object to the processing of your personal data</li>
            <li><strong>Portability:</strong> Request transfer of your data to another service provider</li>
            <li><strong>Withdraw Consent:</strong> Withdraw consent for marketing communications at any time</li>
        </ul>
        <p>
            To exercise any of these rights, please contact us at privacy@tasmiya.com
        </p>
    </div>
    
    <div class="policy-section">
        <h2>7. Cookies and Tracking</h2>
        <p>
            We use cookies and similar tracking technologies to enhance your experience on our website. 
            Cookies help us understand how you use our site and improve our services. You can control 
            cookie settings through your browser preferences.
        </p>
    </div>
    
    <div class="policy-section">
        <h2>8. Children's Privacy</h2>
        <p>
            Our services are not intended for individuals under the age of 18. We do not knowingly 
            collect personal information from children. If you believe we have inadvertently collected 
            such information, please contact us immediately.
        </p>
    </div>
    
    <div class="policy-section">
        <h2>9. Changes to This Policy</h2>
        <p>
            We may update this privacy policy from time to time. We will notify you of any significant 
            changes by posting the new policy on this page and updating the "Last Updated" date. We 
            encourage you to review this policy periodically.
        </p>
    </div>
    
    <div class="policy-section">
        <h2>10. Contact Us</h2>
        <p>
            If you have questions or concerns about this privacy policy or our data practices, please contact us:
        </p>
        <ul>
            <li><strong>Email:</strong> privacy@tasmiya.com</li>
            <li><strong>Phone:</strong> +92-312-4246916</li>
            <li><strong>Address:</strong> Office No.5, First Floor, Mozang Heights, 43 Mozang Rd, Lahore, Pakistan</li>
        </ul>
    </div>
</div>
@endsection
