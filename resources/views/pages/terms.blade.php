@extends('layouts.app')

@section('title', 'Terms of Service - Tasmiya Enterprises')

@section('content')
<style>
    .terms-hero {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        padding: 100px 0 60px;
        text-align: center;
        color: white;
    }
    
    .terms-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }
    
    .terms-hero p {
        font-size: 1.1rem;
        opacity: 0.95;
    }
    
    .terms-content {
        max-width: 900px;
        margin: 60px auto;
        padding: 0 20px;
    }
    
    .terms-section {
        margin-bottom: 40px;
    }
    
    .terms-section h2 {
        font-size: 1.8rem;
        color: #1a202c;
        margin-bottom: 1rem;
        font-weight: 700;
        border-left: 4px solid #f5576c;
        padding-left: 15px;
    }
    
    .terms-section h3 {
        font-size: 1.3rem;
        color: #2d3748;
        margin: 1.5rem 0 0.8rem;
        font-weight: 600;
    }
    
    .terms-section p, .terms-section li {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #4a5568;
        margin-bottom: 1rem;
    }
    
    .terms-section ul, .terms-section ol {
        padding-left: 30px;
        margin-bottom: 1.5rem;
    }
    
    .terms-section li {
        margin-bottom: 0.8rem;
    }
    
    .last-updated {
        background: #fff5f7;
        padding: 15px 20px;
        border-radius: 10px;
        border-left: 4px solid #f5576c;
        margin-bottom: 40px;
        font-weight: 600;
        color: #2d3748;
    }
    
    .important-notice {
        background: #fff5f7;
        border: 2px solid #f5576c;
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
    }
    
    .important-notice p {
        margin: 0;
        color: #742a2a;
        font-weight: 500;
    }
</style>

<div class="terms-hero">
    <h1>Terms of Service</h1>
    <p>Please read these terms carefully before using our services</p>
</div>

<div class="terms-content">
    <div class="last-updated">
        📅 Last Updated: February 11, 2026
    </div>
    
    <div class="terms-section">
        <h2>1. Acceptance of Terms</h2>
        <p>
            By accessing and using the services provided by Tasmiya Enterprises ("we," "us," or "our"), 
            you ("user," "you," or "your") agree to be bound by these Terms of Service. If you do not 
            agree to these terms, please do not use our services.
        </p>
        <div class="important-notice">
            <p>
                ⚠️ <strong>Important:</strong> These terms constitute a legally binding agreement between you 
                and Tasmiya Enterprises. Please read them carefully.
            </p>
        </div>
    </div>
    
    <div class="terms-section">
        <h2>2. Services Description</h2>
        <p>
            Tasmiya Enterprises provides professional business solutions, consulting services, technical 
            support, and related services as described on our website. We reserve the right to modify, 
            suspend, or discontinue any aspect of our services at any time without prior notice.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>3. User Accounts</h2>
        <h3>3.1 Account Creation</h3>
        <p>To access certain features, you may need to create an account. When creating an account, you agree to:</p>
        <ul>
            <li>Provide accurate, current, and complete information</li>
            <li>Maintain and promptly update your account information</li>
            <li>Maintain the security of your password and account</li>
            <li>Accept responsibility for all activities under your account</li>
            <li>Notify us immediately of any unauthorized use</li>
        </ul>
        
        <h3>3.2 Account Termination</h3>
        <p>
            We reserve the right to suspend or terminate your account at any time for violation of these 
            terms or for any other reason we deem appropriate. You may also delete your account at any 
            time by contacting us.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>4. User Conduct</h2>
        <p>When using our services, you agree NOT to:</p>
        <ul>
            <li>Violate any applicable laws or regulations</li>
            <li>Infringe on intellectual property rights of others</li>
            <li>Transmit any harmful or malicious code</li>
            <li>Attempt to gain unauthorized access to our systems</li>
            <li>Harass, abuse, or harm other users</li>
            <li>Use our services for any fraudulent or illegal purpose</li>
            <li>Interfere with or disrupt the integrity of our services</li>
            <li>Collect or harvest personal information of other users</li>
        </ul>
    </div>
    
    <div class="terms-section">
        <h2>5. Intellectual Property</h2>
        <h3>5.1 Our Content</h3>
        <p>
            All content on our website and services, including text, graphics, logos, images, software, 
            and other materials, is the property of Tasmiya Enterprises and is protected by copyright, 
            trademark, and other intellectual property laws.
        </p>
        
        <h3>5.2 License Grant</h3>
        <p>
            We grant you a limited, non-exclusive, non-transferable license to access and use our services 
            for personal or business purposes in accordance with these terms.
        </p>
        
        <h3>5.3 User Content</h3>
        <p>
            You retain ownership of any content you submit to our services. By submitting content, you 
            grant us a worldwide, non-exclusive, royalty-free license to use, reproduce, and display your 
            content for the purpose of providing our services.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>6. Payment Terms</h2>
        <h3>6.1 Fees and Charges</h3>
        <p>
            Certain services may require payment of fees. All fees are stated in the currency specified 
            on our website and are subject to change with notice. You agree to pay all applicable fees 
            associated with your use of our services.
        </p>
        
        <h3>6.2 Payment Processing</h3>
        <p>
            Payments are processed through secure third-party payment processors. We do not store your 
            complete payment card information. You agree to provide current, complete, and accurate 
            billing information.
        </p>
        
        <h3>6.3 Refunds</h3>
        <p>
            Refund policies vary by service and will be clearly communicated at the time of purchase. 
            Unless otherwise stated, all sales are final.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>7. Disclaimers and Warranties</h2>
        <p>
            Our services are provided "as is" and "as available" without warranties of any kind, either 
            express or implied. We do not warrant that:
        </p>
        <ul>
            <li>Our services will be uninterrupted, timely, secure, or error-free</li>
            <li>The results obtained from using our services will be accurate or reliable</li>
            <li>Any errors in our services will be corrected</li>
            <li>Our services will meet your specific requirements</li>
        </ul>
    </div>
    
    <div class="terms-section">
        <h2>8. Limitation of Liability</h2>
        <p>
            To the maximum extent permitted by law, Tasmiya Enterprises shall not be liable for any 
            indirect, incidental, special, consequential, or punitive damages, including but not limited 
            to loss of profits, data, or other intangible losses, resulting from:
        </p>
        <ul>
            <li>Your use or inability to use our services</li>
            <li>Any unauthorized access to or use of our servers</li>
            <li>Any interruption or cessation of our services</li>
            <li>Any bugs, viruses, or similar harmful components</li>
            <li>Any errors or omissions in content</li>
        </ul>
    </div>
    
    <div class="terms-section">
        <h2>9. Indemnification</h2>
        <p>
            You agree to indemnify, defend, and hold harmless Tasmiya Enterprises, its officers, directors, 
            employees, and agents from any claims, liabilities, damages, losses, and expenses, including 
            reasonable attorneys' fees, arising out of your use of our services or violation of these terms.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>10. Privacy</h2>
        <p>
            Your use of our services is also governed by our Privacy Policy, which is incorporated into 
            these terms by reference. Please review our Privacy Policy to understand our data practices.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>11. Modifications to Terms</h2>
        <p>
            We reserve the right to modify these Terms of Service at any time. We will notify you of 
            material changes by posting the updated terms on our website and updating the "Last Updated" 
            date. Your continued use of our services after such changes constitutes acceptance of the 
            new terms.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>12. Governing Law</h2>
        <p>
            These terms shall be governed by and construed in accordance with the laws of Pakistan, 
            without regard to its conflict of law provisions. Any disputes arising from these terms or 
            your use of our services shall be subject to the exclusive jurisdiction of the courts in 
            Lahore, Pakistan (Office No.5, First Floor, Mozang Heights, 43 Mozang Rd, Lahore).
        </p>
    </div>
    
    <div class="terms-section">
        <h2>13. Severability</h2>
        <p>
            If any provision of these terms is found to be unenforceable or invalid, that provision shall 
            be limited or eliminated to the minimum extent necessary, and the remaining provisions shall 
            remain in full force and effect.
        </p>
    </div>
    
    <div class="terms-section">
        <h2>14. Contact Information</h2>
        <p>
            If you have questions or concerns about these Terms of Service, please contact us:
        </p>
        <ul>
            <li><strong>Email:</strong> legal@tasmiya.com</li>
            <li><strong>Phone:</strong> +92-312-4246916</li>
            <li><strong>Address:</strong> Office No.5, First Floor, Mozang Heights, 43 Mozang Rd, Lahore, Pakistan</li>
        </ul>
    </div>
    
    <div class="terms-section">
        <h2>15. Entire Agreement</h2>
        <p>
            These Terms of Service, together with our Privacy Policy and any other legal notices published 
            on our website, constitute the entire agreement between you and Tasmiya Enterprises regarding 
            your use of our services.
        </p>
    </div>
</div>
@endsection
