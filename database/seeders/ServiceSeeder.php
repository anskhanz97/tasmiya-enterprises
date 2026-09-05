<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Division;
use App\Models\Profile;
use Illuminate\Database\Seeder;

/**
 * ServiceSeeder
 *
 * Seeds the services table with demo services for Tasmiya Enterprises.
 *
 * Services Created:
 * 1. FBR Taxation Division (10 services)
 *    - Tax Consultation
 *    - Compliance Audit
 *    - Tax Planning & Strategy
 *    - Corporate Tax
 *    - Freelancer Tax
 *    - Withholding Tax
 *    - Sales Tax Return Filing
 *    - Income Tax Return
 *    - Tax Registration Services
 *    - VAT & Excise Duty
 *
 * 2. IT & Digital Division (10 services)
 *    - Web Development
 *    - Mobile App Development
 *    - Cloud Solutions
 *    - System Administration
 *    - Cloud Migration
 *    - IT Consulting
 *    - Digital Marketing
 *    - E-commerce Solutions
 *    - Database Management
 *    - UI/UX Design
 *
 * 3. Tech Support Division (8 services)
 *    - Hardware Support
 *    - Software Support
 *    - Network Support
 *    - System Maintenance
 *    - Data Recovery
 *    - Security Solutions
 *    - Remote IT Support
 *    - Printer & Peripheral Support
 *
 * Each service is assigned to relevant profiles (experts).
 *
 * Total: 32 services across 3 divisions
 */
class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get divisions
        $fbrDivision = Division::where('slug', 'fbr-taxation')->first();
        $itDivision = Division::where('slug', 'it-digital')->first();
        $techDivision = Division::where('slug', 'tech-support')->first();

        // Get profiles for assignment
        $atif = Profile::whereHas('user', fn($q) => $q->where('email', 'atif@tasmiya.com'))->first();
        $waseem = Profile::whereHas('user', fn($q) => $q->where('email', 'waseem@tasmiya.com'))->first();
        $ans = Profile::whereHas('user', fn($q) => $q->where('email', 'ans@tasmiya.com'))->first();
        $nazim = Profile::whereHas('user', fn($q) => $q->where('email', 'nazim@tasmiya.com'))->first();

        // ==========================================
        // FBR TAXATION DIVISION SERVICES
        // ==========================================

        // 1. Tax Consultation
        $service1 = Service::create([
            'name' => 'Tax Consultation',
            'slug' => 'tax-consultation',
            'description' => 'Expert guidance on all aspects of taxation. Get personalized advice from experienced tax consultants to optimize your tax position.',
            'long_description' => 'Our tax consultation service provides comprehensive guidance on personal and corporate taxation. We analyze your financial situation and provide actionable recommendations to minimize tax liability while ensuring full compliance with FBR regulations.',
            'base_price' => 5000.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service1->profiles()->attach($atif->id);
        }

        // 2. Compliance Audit
        $service2 = Service::create([
            'name' => 'Compliance Audit',
            'slug' => 'compliance-audit',
            'description' => 'Comprehensive audit to ensure full compliance with tax regulations. Identify gaps and risks before they become problems.',
            'long_description' => 'We conduct detailed compliance audits to review your financial records and tax filings. Our audits help identify any gaps or risks, ensuring you remain fully compliant with FBR requirements.',
            'base_price' => 8000.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service2->profiles()->attach($atif->id);
        }

        // 3. Tax Planning & Strategy
        $service3 = Service::create([
            'name' => 'Tax Planning & Strategy',
            'slug' => 'tax-planning-strategy',
            'description' => 'Proactive tax planning to legally reduce your tax burden. Strategic financial planning tailored to your business.',
            'long_description' => 'Our tax planning service helps you develop strategies to legally minimize taxes while maximizing business efficiency. We work with you year-round to identify opportunities and structure your finances optimally.',
            'base_price' => 10000.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service3->profiles()->attach($atif->id);
        }

        // 4. Corporate Tax
        $service4 = Service::create([
            'name' => 'Corporate Tax',
            'slug' => 'corporate-tax',
            'description' => 'Corporate taxation for businesses of all sizes. From startups to enterprises, we handle your corporate tax needs.',
            'long_description' => 'We provide comprehensive corporate tax services including annual tax returns, quarterly filing, corporate restructuring, and compliance. Our expertise covers all business structures and industries.',
            'base_price' => 12000.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service4->profiles()->attach($atif->id);
        }

        // 5. Freelancer Tax
        $service5 = Service::create([
            'name' => 'Freelancer Tax',
            'slug' => 'freelancer-tax',
            'description' => 'Specialized tax services for freelancers and contractors. Simplified tax compliance for independent professionals.',
            'long_description' => 'Freelancers need special tax handling. We help you understand your tax obligations, prepare accurate filings, and stay compliant while maximizing deductions.',
            'base_price' => 3000.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service5->profiles()->attach($atif->id);
        }

        // 6. Withholding Tax
        $service6 = Service::create([
            'name' => 'Withholding Tax',
            'slug' => 'withholding-tax',
            'description' => 'Withholding tax compliance and management. Ensure proper withholding on salaries, payments, and transactions.',
            'long_description' => 'Complex withholding tax rules require expertise. We help you understand and manage withholding tax obligations on various payments and transactions.',
            'base_price' => 4000.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service6->profiles()->attach($atif->id);
        }

        // 7. Sales Tax Return Filing
        $service17 = Service::create([
            'name' => 'Sales Tax Return Filing',
            'slug' => 'sales-tax-return-filing',
            'description' => 'Monthly and quarterly sales tax returns. Complete documentation and timely filing for registered businesses.',
            'long_description' => 'We handle all aspects of sales tax return preparation and filing. Our service ensures accurate calculations, proper documentation, and timely submission to avoid penalties.',
            'base_price' => 6000.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service17->profiles()->attach($atif->id);
        }

        // 8. Income Tax Return
        $service18 = Service::create([
            'name' => 'Income Tax Return',
            'slug' => 'income-tax-return',
            'description' => 'Personal and business income tax returns. Professional preparation ensuring maximum legal deductions.',
            'long_description' => 'Expert preparation of income tax returns for individuals and businesses. We ensure accuracy, optimize deductions, and handle all communications with FBR on your behalf.',
            'base_price' => 4500.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service18->profiles()->attach($atif->id);
        }

        // 9. Tax Registration Services
        $service19 = Service::create([
            'name' => 'Tax Registration Services',
            'slug' => 'tax-registration-services',
            'description' => 'NTN, STRN, and CNIC registration with FBR. Complete registration assistance for new businesses.',
            'long_description' => 'We facilitate the registration process with FBR including National Tax Number (NTN), Sales Tax Registration Number (STRN), and all required documentation.',
            'base_price' => 3500.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service19->profiles()->attach($atif->id);
        }

        // 10. VAT & Excise Duty
        $service20 = Service::create([
            'name' => 'VAT & Excise Duty',
            'slug' => 'vat-excise-duty',
            'description' => 'Value Added Tax and excise duty compliance. Specialized handling for manufacturing and import businesses.',
            'long_description' => 'Expert guidance on VAT and excise duty matters. We handle registrations, returns, refunds, and ensure compliance with complex regulations.',
            'base_price' => 7500.00,
            'currency' => 'PKR',
            'division_id' => $fbrDivision->id,
            'is_active' => true,
        ]);
        if ($atif) {
            $service20->profiles()->attach($atif->id);
        }

        // ==========================================
        // IT & DIGITAL DIVISION SERVICES
        // ==========================================

        // 11. Web Development
        $service11 = Service::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Custom web applications tailored to your business. Modern, responsive, and high-performance websites.',
            'long_description' => 'From simple websites to complex web applications, we build custom solutions using the latest technologies. Our team delivers responsive, fast, and scalable web applications.',
            'base_price' => 50000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($waseem) {
            $service11->profiles()->attach($waseem->id);
        }
        if ($ans) {
            $service11->profiles()->attach($ans->id);
        }

        // 12. Mobile App Development
        $service12 = Service::create([
            'name' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'description' => 'Native and cross-platform mobile applications. iOS, Android, and progressive web apps.',
            'long_description' => 'We develop mobile apps for iOS and Android using native or cross-platform technologies. Our apps are user-friendly, performant, and maintain your brand identity.',
            'base_price' => 60000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($ans) {
            $service12->profiles()->attach($ans->id);
        }

        // 13. Cloud Solutions
        $service13 = Service::create([
            'name' => 'Cloud Solutions',
            'slug' => 'cloud-solutions',
            'description' => 'Cloud infrastructure setup and management. AWS, Azure, Google Cloud solutions for scalable systems.',
            'long_description' => 'We architect and implement cloud solutions on major platforms. Our solutions are secure, scalable, and cost-optimized for your business needs.',
            'base_price' => 40000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($waseem) {
            $service13->profiles()->attach($waseem->id);
        }

        // 14. System Administration
        $service14 = Service::create([
            'name' => 'System Administration',
            'slug' => 'system-administration',
            'description' => 'Server and system management. Windows, Linux server administration and support.',
            'long_description' => 'We manage and maintain your servers ensuring uptime, security, and optimal performance. Services include updates, patches, monitoring, and troubleshooting.',
            'base_price' => 8000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($waseem) {
            $service14->profiles()->attach($waseem->id);
        }

        // 15. Cloud Migration
        $service15 = Service::create([
            'name' => 'Cloud Migration',
            'slug' => 'cloud-migration',
            'description' => 'Seamless migration of your systems to the cloud. Zero downtime migration services.',
            'long_description' => 'We plan and execute cloud migrations with minimal disruption. Our expertise ensures data integrity, security, and smooth transition to cloud infrastructure.',
            'base_price' => 35000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($waseem) {
            $service15->profiles()->attach($waseem->id);
        }

        // 16. IT Consulting
        $service16 = Service::create([
            'name' => 'IT Consulting',
            'slug' => 'it-consulting',
            'description' => 'Strategic IT consulting for digital transformation. Technology roadmaps and implementation guidance.',
            'long_description' => 'Our IT consultants help you develop technology strategies aligned with business goals. We provide guidance on infrastructure, security, and digital transformation initiatives.',
            'base_price' => 15000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($waseem) {
            $service16->profiles()->attach($waseem->id);
        }

        // 17. Digital Marketing
        $service21 = Service::create([
            'name' => 'Digital Marketing',
            'slug' => 'digital-marketing',
            'description' => 'Comprehensive digital marketing strategies. SEO, social media, and online advertising campaigns.',
            'long_description' => 'Boost your online presence with our digital marketing services. We handle SEO, social media marketing, content strategy, and paid advertising to grow your business.',
            'base_price' => 25000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($ans) {
            $service21->profiles()->attach($ans->id);
        }

        // 18. E-commerce Solutions
        $service22 = Service::create([
            'name' => 'E-commerce Solutions',
            'slug' => 'ecommerce-solutions',
            'description' => 'Complete online store development. Shopping carts, payment gateways, and inventory management.',
            'long_description' => 'Launch your online store with our e-commerce solutions. We build custom platforms with secure payments, inventory management, and seamless user experiences.',
            'base_price' => 45000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($waseem) {
            $service22->profiles()->attach($waseem->id);
        }
        if ($ans) {
            $service22->profiles()->attach($ans->id);
        }

        // 19. Database Management
        $service23 = Service::create([
            'name' => 'Database Management',
            'slug' => 'database-management',
            'description' => 'Database design, optimization, and administration. MySQL, PostgreSQL, MongoDB, and SQL Server.',
            'long_description' => 'Expert database services including design, optimization, backup, recovery, and performance tuning. We work with all major database platforms.',
            'base_price' => 12000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($waseem) {
            $service23->profiles()->attach($waseem->id);
        }

        // 20. UI/UX Design
        $service24 = Service::create([
            'name' => 'UI/UX Design',
            'slug' => 'ui-ux-design',
            'description' => 'User interface and experience design. Modern, intuitive designs that convert visitors to customers.',
            'long_description' => 'Create stunning user experiences with our UI/UX design services. We focus on usability, aesthetics, and conversion optimization.',
            'base_price' => 20000.00,
            'currency' => 'PKR',
            'division_id' => $itDivision->id,
            'is_active' => true,
        ]);
        if ($ans) {
            $service24->profiles()->attach($ans->id);
        }

        // ==========================================
        // TECH SUPPORT DIVISION SERVICES
        // ==========================================

        // 21. Hardware Support
        $service25 = Service::create([
            'name' => 'Hardware Support',
            'slug' => 'hardware-support',
            'description' => 'Repair and maintenance of computer hardware. Desktop, laptop, and server repairs.',
            'long_description' => 'We diagnose and repair hardware issues quickly. From component replacement to system troubleshooting, our technicians are equipped to handle all hardware needs.',
            'base_price' => 2000.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service25->profiles()->attach($nazim->id);
        }

        // 22. Software Support
        $service26 = Service::create([
            'name' => 'Software Support',
            'slug' => 'software-support',
            'description' => 'Software installation, configuration, and troubleshooting. Operating systems and applications.',
            'long_description' => 'We help with software installation, configuration, and issue resolution. Our technicians can help with Windows, Mac, Linux, and common applications.',
            'base_price' => 1500.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service26->profiles()->attach($nazim->id);
        }

        // 23. Network Support
        $service27 = Service::create([
            'name' => 'Network Support',
            'slug' => 'network-support',
            'description' => 'Network setup, troubleshooting, and optimization. WiFi, LAN, and internet connectivity.',
            'long_description' => 'We diagnose and resolve network issues. Our services include WiFi setup, network optimization, connectivity troubleshooting, and performance improvement.',
            'base_price' => 3000.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service27->profiles()->attach($nazim->id);
        }

        // 24. System Maintenance
        $service28 = Service::create([
            'name' => 'System Maintenance',
            'slug' => 'system-maintenance',
            'description' => 'Preventive maintenance to keep systems running smoothly. Regular updates and optimization.',
            'long_description' => 'Preventive maintenance extends system life and prevents problems. We handle updates, cleanup, optimization, and regular health checks.',
            'base_price' => 2500.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service28->profiles()->attach($nazim->id);
        }

        // 25. Data Recovery
        $service29 = Service::create([
            'name' => 'Data Recovery',
            'slug' => 'data-recovery',
            'description' => 'Professional data recovery services. Recover lost data from hard drives, SSDs, and storage devices.',
            'long_description' => 'Lost important data? Our data recovery specialists use advanced tools and techniques to recover data from failed drives, corrupted storage, and accidental deletions.',
            'base_price' => 5000.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service29->profiles()->attach($nazim->id);
        }

        // 26. Security Solutions
        $service30 = Service::create([
            'name' => 'Security Solutions',
            'slug' => 'security-solutions',
            'description' => 'Cybersecurity and antivirus protection. Secure your systems from threats and malware.',
            'long_description' => 'Protect your systems with our security solutions including antivirus setup, firewall configuration, security audits, and threat removal.',
            'base_price' => 4000.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service30->profiles()->attach($nazim->id);
        }

        // 27. Remote IT Support
        $service31 = Service::create([
            'name' => 'Remote IT Support',
            'slug' => 'remote-it-support',
            'description' => 'Fast remote technical assistance. Quick solutions without on-site visits for urgent issues.',
            'long_description' => 'Get immediate technical help through remote support. Our technicians can diagnose and fix many issues remotely, saving time and costs.',
            'base_price' => 1000.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service31->profiles()->attach($nazim->id);
        }

        // 28. Printer & Peripheral Support
        $service32 = Service::create([
            'name' => 'Printer & Peripheral Support',
            'slug' => 'printer-peripheral-support',
            'description' => 'Printer setup, troubleshooting, and peripheral device support. Network printers and scanners.',
            'long_description' => 'Complete support for printers, scanners, and other peripherals. We handle installation, configuration, driver issues, and troubleshooting.',
            'base_price' => 1800.00,
            'currency' => 'PKR',
            'division_id' => $techDivision->id,
            'is_active' => true,
        ]);
        if ($nazim) {
            $service32->profiles()->attach($nazim->id);
        }

        $this->command->info('✓ Services seeded successfully! (32 services created)');
    }
}
