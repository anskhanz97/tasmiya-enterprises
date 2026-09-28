<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Profile Seeder - Create demo profiles for all team members
 * 
 * Purpose:
 * Populates the profiles table with realistic profile information
 * for each of the 4 team members created in UserSeeder
 * 
 * Data includes:
 * - Biography
 * - Specializations/skills
 * - Experience years
 * - Social links (WhatsApp, LinkedIn)
 * - Qualifications/certifications
 * - Languages spoken
 * - Consultation fees
 * 
 * Usage:
 * php artisan db:seed --class=ProfileSeeder
 * 
 * Or in full seeding:
 * php artisan db:seed
 * (if called from DatabaseSeeder)
 * 
 * Why separate seeder?
 * - UserSeeder creates the users (Phase 2)
 * - ProfileSeeder creates profiles (Phase 3)
 * - Keeps seeders organized by phase
 * - Can be run independently
 * 
 * Data Structure:
 * Each profile linked to user via user_id
 * Profile data separated from user data (separation of concerns)
 */
class ProfileSeeder extends Seeder
{
    /**
     * Seed the profiles table
     */
    public function run(): void
    {
        // Get all users (created by UserSeeder)
        $atif = User::where('email', 'atif@tasmiya.com')->first();
        $waseem = User::where('email', 'waseem@tasmiya.com')->first();
        $ans = User::where('email', 'ans@tasmiya.com')->first();
        $nazim = User::where('email', 'nazim@tasmiya.com')->first();

        // Create profile for Atif Safdar - Legal & Tax Advisory Specialist
        Profile::create([
            'user_id' => $atif->id,
            
            // Personal Bio
            'bio' => 'With a strong understanding of legal compliance and tax regulations, this consultant provides practical, client-focused solutions for individuals and businesses. Known for a professional yet approachable manner, ensuring clarity, accuracy, and confidence in every consultation.',
            
            // Skills & Specializations
            'specializations' => [
                'FBR Tax Compliance',
                'Corporate Taxation',
                'Income Tax Planning',
                'Tax Audit Support',
                'Regulatory Compliance',
                'Tax Optimization',
            ],
            
            // Experience
            'experience_years' => 12,
            
            // Optimized local defaults; admins can replace either image in the portal.
            'profile_image_url' => '/images/profiles/atif/image.webp',
            'banner_image_url' => '/images/profiles/atif/banner.webp',
            
            // Contact Information (WhatsApp, LinkedIn, etc.)
            'social_links' => [
                'whatsapp' => '+92-312-4246916',
                'linkedin' => 'linkedin.com/in/atifsafdar',
                'email' => 'atif@tasmiya.com',
                'phone' => '+92-312-4246916',
            ],
            
            // Education & Certifications
            'qualifications' => [
                'Bachelor of Commerce (B.Com)',
                'FBR Tax Certificate',
                'Advanced Tax Planning Course',
                'Corporate Finance Diploma',
            ],
            
            // Languages Spoken
            'languages' => [
                'Urdu',
                'English',
                'Arabic',
            ],
            
            // Service Fee (hourly consultation rate)
            'consultation_fee' => 5000.00, // 5000 PKR per hour
            
            // Visibility (shown in team showcase)
            'is_visible' => true,
        ]);

        // Create profile for Waseem Asghar - Creative Technology & Full-Stack Expert
        Profile::create([
            'user_id' => $waseem->id,
            
            // Personal Bio
            'bio' => 'Specializing in AI-powered visual content, branding, and digital promotion, he bridges creativity with technology. With expertise in full-stack development and modern marketing strategies, he delivers impactful digital experiences that elevate brands and drive engagement.',
            
            'specializations' => [
                'AI-Powered Visual Content',
                'Digital Branding',
                'Full-Stack Development',
                'Digital Promotion',
                'Marketing Technology',
                'Brand Elevation',
            ],
            'experience_years' => 4,
            'profile_image_url' => '/images/profiles/waseem/image.webp',
            'banner_image_url' => '/images/profiles/waseem/banner.webp',
            'social_links' => [
                'whatsapp' => '+92-303-4829937',
                'linkedin' => 'linkedin.com/in/waseemasghar',
                'email' => 'waseem@tasmiya.com',
                'phone' => '+92-303-4829937',
            ],
            'qualifications' => [
                'BS Computer Science',
                'AI & Machine Learning Specialist',
                'Full-Stack Development',
                'Digital Marketing Expert',
            ],
            'languages' => [
                'Urdu',
                'English',
                'Hindi',
            ],
            'consultation_fee' => 4000.00,
            'is_visible' => true,
        ]);

        // Create profile for Ans Khan - Backend Software Engineer (PHP Laravel)
        Profile::create([
            'user_id' => $ans->id,
            
            // Personal Bio
            'bio' => 'With over three years of experience in backend web development, he focuses on building stable, efficient, and secure server-side applications using PHP Laravel. His work emphasizes reliability, clean architecture, and long-term maintainability.',
            
            'specializations' => [
                'Backend Web Development',
                'PHP Laravel',
                'Server-Side Applications',
                'API Development',
                'Clean Architecture',
                'Database-Driven Design',
            ],
            'experience_years' => 3,
            'profile_image_url' => '/images/profiles/ans/image.webp',
            'banner_image_url' => '/images/profiles/ans/banner.webp',
            'social_links' => [
                'whatsapp' => '+92-305-1852884',
                'linkedin' => 'linkedin.com/in/anskhan',
                'email' => 'ans@tasmiya.com',
                'phone' => '+92-305-1852884',
            ],
            'qualifications' => [
                'BS Information Technology',
                'Laravel Certified Developer',
                'Backend Development Specialist',
                'Database Design Expert',
            ],
            'languages' => [
                'Urdu',
                'English',
            ],
            'consultation_fee' => 3500.00,
            'is_visible' => true,
        ]);

        // Create profile for Nazim Rauf - Technical Systems & Automation Specialist
        Profile::create([
            'user_id' => $nazim->id,
            
            // Personal Bio
            'bio' => 'A highly active field expert with hands-on experience in security systems, networking, and automation. With over 5 years of proven expertise, he brings practical solutions and technical excellence to every project.',
            
            'specializations' => [
                'Security Systems',
                'Networking',
                'Automation & Scripting',
                'Infrastructure Management',
                'Field Expertise',
                'Technical Solutions',
            ],
            'experience_years' => 5,
            'profile_image_url' => '/images/profiles/nazim/image.webp',
            'banner_image_url' => '/images/profiles/nazim/banner.webp',
            'social_links' => [
                'whatsapp' => '+92-320-5889344',
                'linkedin' => 'linkedin.com/in/nazimrauf',
                'email' => 'nazim@tasmiya.com',
                'phone' => '+92-320-5889344',
            ],
            'qualifications' => [
                'Security Systems Certification',
                'Network Administration',
                'Automation & Scripting Specialist',
                'Infrastructure Management Expert',
            ],
            'languages' => [
                'Urdu',
                'English',
                'Pashto',
            ],
            'consultation_fee' => 2500.00,
            'is_visible' => true,
        ]);

        // Output confirmation
        $this->command->info('✓ Profiles seeded successfully!');
        $this->command->info('  - Atif Safdar (FBR Taxation)');
        $this->command->info('  - Waseem Asghar (IT & Digital)');
        $this->command->info('  - Ans Khan (IT & Digital)');
        $this->command->info('  - Nazim Rauf (Tech Support)');
    }
}
