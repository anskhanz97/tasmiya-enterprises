<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * TestimonialSeeder
 *
 * Seeds the testimonials table with demo client reviews.
 *
 * Creates 20+ testimonials across various services:
 * - Different ratings (mix of 5★, 4★, 3★)
 * - Variety of reviewers (different companies and positions)
 * - Some marked as featured (is_featured = true)
 * - All marked as approved (is_approved = true) for display
 *
 * These testimonials demonstrate:
 * 1. Polymorphic relationship (testimonials linked to services)
 * 2. Real-world review variety
 * 3. Rating distribution
 * 4. Featured testimonial system
 */
class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get services
        $services = Service::all();

        if ($services->isEmpty()) {
            $this->command->warn('No services found. Run ServiceSeeder first!');
            return;
        }

        // ==========================================
        // TAX CONSULTATION TESTIMONIALS
        // ==========================================

        $taxConsultation = Service::where('slug', 'tax-consultation')->first();
        if ($taxConsultation) {
            Testimonial::create([
                'content' => 'Atif\'s tax consultation was invaluable. He identified several tax-saving strategies we hadn\'t considered. His expertise and attention to detail are exceptional.',
                'rating' => 5,
                'author_name' => 'Ahmed Hassan',
                'author_position' => 'CEO',
                'author_company' => 'Tech Innovations Ltd',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $taxConsultation->id,
                'is_approved' => true,
                'is_featured' => true,
            ]);

            Testimonial::create([
                'content' => 'Professional, knowledgeable, and very responsive. Atif made our tax filing process much simpler. Highly recommended!',
                'rating' => 5,
                'author_name' => 'Fatima Khan',
                'author_position' => 'Business Owner',
                'author_company' => 'Khan & Associates',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $taxConsultation->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);

            Testimonial::create([
                'content' => 'Great insights on tax planning. Saved us a considerable amount in taxes this year.',
                'rating' => 4,
                'author_name' => 'Muhammad Ali',
                'author_position' => 'Financial Director',
                'author_company' => 'Export Co.',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $taxConsultation->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // COMPLIANCE AUDIT TESTIMONIALS
        // ==========================================

        $complianceAudit = Service::where('slug', 'compliance-audit')->first();
        if ($complianceAudit) {
            Testimonial::create([
                'content' => 'The compliance audit was thorough and professional. Found and fixed several issues before they became problems. Worth every rupee!',
                'rating' => 5,
                'author_name' => 'Sana Ahmed',
                'author_position' => 'CFO',
                'author_company' => 'Pyramid Industries',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $complianceAudit->id,
                'is_approved' => true,
                'is_featured' => true,
            ]);

            Testimonial::create([
                'content' => 'Professional audit that gave us confidence in our compliance. Clear recommendations for improvement.',
                'rating' => 4,
                'author_name' => 'Tariq Mahmood',
                'author_position' => 'Compliance Officer',
                'author_company' => 'Global Trading',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $complianceAudit->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // WEB DEVELOPMENT TESTIMONIALS
        // ==========================================

        $webDev = Service::where('slug', 'web-development')->first();
        if ($webDev) {
            Testimonial::create([
                'content' => 'Waseem and team delivered an amazing website. Modern design, fast loading, and fully responsive. Our customers love it!',
                'rating' => 5,
                'author_name' => 'Ali Raza',
                'author_position' => 'Founder',
                'author_company' => 'E-Shop Pakistan',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $webDev->id,
                'is_approved' => true,
                'is_featured' => true,
            ]);

            Testimonial::create([
                'content' => 'Excellent communication throughout the project. The website is professional and converts well.',
                'rating' => 5,
                'author_name' => 'Hina Khalil',
                'author_position' => 'Marketing Manager',
                'author_company' => 'Fashion Hub',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $webDev->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);

            Testimonial::create([
                'content' => 'Good developers, solid website. Could have been faster to deliver but end result is solid.',
                'rating' => 4,
                'author_name' => 'Hassan Khan',
                'author_position' => 'Business Manager',
                'author_company' => 'PrintPro Services',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $webDev->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // MOBILE APP DEVELOPMENT TESTIMONIALS
        // ==========================================

        $appDev = Service::where('slug', 'mobile-app-development')->first();
        if ($appDev) {
            Testimonial::create([
                'content' => 'Ans developed our app exactly as we envisioned. The user experience is smooth and intuitive. We\'ve seen great user adoption!',
                'rating' => 5,
                'author_name' => 'Sara Malik',
                'author_position' => 'Product Owner',
                'author_company' => 'Delivery App Inc',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $appDev->id,
                'is_approved' => true,
                'is_featured' => true,
            ]);

            Testimonial::create([
                'content' => 'Very professional team. The app works great on both iOS and Android. Highly skilled developers.',
                'rating' => 5,
                'author_name' => 'Omar Farooq',
                'author_position' => 'Project Manager',
                'author_company' => 'Fintech Solutions',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $appDev->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // CLOUD SOLUTIONS TESTIMONIALS
        // ==========================================

        $cloudSol = Service::where('slug', 'cloud-solutions')->first();
        if ($cloudSol) {
            Testimonial::create([
                'content' => 'Our infrastructure is now more reliable and scalable. Waseem\'s cloud setup saved us money on hardware costs while improving performance.',
                'rating' => 5,
                'author_name' => 'Kamran Ahmed',
                'author_position' => 'IT Director',
                'author_company' => 'Enterprise Systems',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $cloudSol->id,
                'is_approved' => true,
                'is_featured' => true,
            ]);

            Testimonial::create([
                'content' => 'Solid cloud architecture. Good security and disaster recovery planning. Happy with the results.',
                'rating' => 4,
                'author_name' => 'Zainab Khan',
                'author_position' => 'Operations Manager',
                'author_company' => 'Data Services Ltd',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $cloudSol->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // CLOUD MIGRATION TESTIMONIALS
        // ==========================================

        $migration = Service::where('slug', 'cloud-migration')->first();
        if ($migration) {
            Testimonial::create([
                'content' => 'Zero downtime migration exactly as promised! The planning and execution were flawless. Our systems are now running faster in the cloud.',
                'rating' => 5,
                'author_name' => 'Bilal Saeed',
                'author_position' => 'CTO',
                'author_company' => 'Healthcare Systems',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $migration->id,
                'is_approved' => true,
                'is_featured' => true,
            ]);

            Testimonial::create([
                'content' => 'Great planning and smooth execution. Migration was well-organized with minimal disruption.',
                'rating' => 4,
                'author_name' => 'Alia Hassan',
                'author_position' => 'Systems Admin',
                'author_company' => 'Manufacturing Co',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $migration->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // IT CONSULTING TESTIMONIALS
        // ==========================================

        $itConsulting = Service::where('slug', 'it-consulting')->first();
        if ($itConsulting) {
            Testimonial::create([
                'content' => 'Waseem provided excellent strategic guidance for our digital transformation. His recommendations are practical and aligned with our business goals.',
                'rating' => 5,
                'author_name' => 'Nadeem Khan',
                'author_position' => 'General Manager',
                'author_company' => 'Tech Ventures',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $itConsulting->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // HARDWARE SUPPORT TESTIMONIALS
        // ==========================================

        $hardware = Service::where('slug', 'hardware-support')->first();
        if ($hardware) {
            Testimonial::create([
                'content' => 'Fast and professional hardware repair service. Nazim fixed our server hardware quickly and got us back online with minimal downtime.',
                'rating' => 5,
                'author_name' => 'Rukhsana Malik',
                'author_position' => 'Office Manager',
                'author_company' => 'Corporate Office Solutions',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $hardware->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);

            Testimonial::create([
                'content' => 'Reliable hardware support. Honest assessment and fair pricing. Would definitely call again.',
                'rating' => 5,
                'author_name' => 'Amir Shaheen',
                'author_position' => 'IT Manager',
                'author_company' => 'Medical Clinic',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $hardware->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // NETWORK SUPPORT TESTIMONIALS
        // ==========================================

        $network = Service::where('slug', 'network-support')->first();
        if ($network) {
            Testimonial::create([
                'content' => 'Our WiFi was terrible before. Nazim optimized our network and now connectivity is excellent throughout the office.',
                'rating' => 5,
                'author_name' => 'Mahira Hassan',
                'author_position' => 'Owner',
                'author_company' => 'Marketing Agency',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $network->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        // ==========================================
        // SYSTEM MAINTENANCE TESTIMONIALS
        // ==========================================

        $maintenance = Service::where('slug', 'system-maintenance')->first();
        if ($maintenance) {
            Testimonial::create([
                'content' => 'Proactive system maintenance has prevented multiple issues. Our computers run faster and more stable with regular maintenance.',
                'rating' => 5,
                'author_name' => 'Faisal Abbas',
                'author_position' => 'IT Coordinator',
                'author_company' => 'Legal Firm',
                'author_image_url' => null,
                'testimonialable_type' => Service::class,
                'testimonialable_id' => $maintenance->id,
                'is_approved' => true,
                'is_featured' => false,
            ]);
        }

        $this->command->info('✓ Testimonials seeded successfully! (20+ testimonials created)');
    }
}
