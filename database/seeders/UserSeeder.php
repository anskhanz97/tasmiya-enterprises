<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * UserSeeder - Create demo team members for testing
 * 
 * Creates 4 team members:
 * 1. Atif Safdar (FBR Taxation) - Admin
 * 2. Waseem Asghar (IT & Digital) - Team Member
 * 3. Ans Khan (IT & Digital) - Team Member
 * 4. Nazim Rauf (Technical Support) - Team Member
 * 
 * Run with: php artisan db:seed --class=UserSeeder
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Team member 1: Atif Safdar (FBR Taxation - Admin)
        User::create([
            'name' => 'Atif Safdar',
            'email' => 'atif@tasmiya.com',
            'password' => 'Password@123', // Will be auto-hashed
            'role' => 'admin',
            'division_id' => 1, // FBR Taxation
            'is_active' => true,
            'phone' => '+92-300-1234567',
            'whatsapp_number' => '+92-300-1234567',
            'bio' => 'Tax expert with 15+ years of experience in FBR compliance, income tax filing, GST management, and property tax optimization. Committed to helping businesses and individuals navigate complex taxation rules.',
            'profile_image_url' => '/images/profiles/atif/image.webp',
        ]);

        // Team member 2: Waseem Asghar (IT & Digital - Team Member)
        User::create([
            'name' => 'Waseem Asghar',
            'email' => 'waseem@tasmiya.com',
            'password' => 'Password@123',
            'role' => 'team_member',
            'division_id' => 2, // IT & Digital
            'is_active' => true,
            'phone' => '+92-300-2345678',
            'whatsapp_number' => '+92-300-2345678',
            'bio' => 'Full-stack web developer and digital marketing specialist. Expert in Laravel, React, Vue.js, and cloud deployment. Passionate about creating innovative digital solutions that drive business growth.',
            'profile_image_url' => '/images/profiles/waseem/image.webp',
        ]);

        // Team member 3: Ans Khan (IT & Digital - Team Member)
        User::create([
            'name' => 'Ans Khan',
            'email' => 'ans@tasmiya.com',
            'password' => 'Password@123',
            'role' => 'team_member',
            'division_id' => 2, // IT & Digital
            'is_active' => true,
            'phone' => '+92-300-3456789',
            'whatsapp_number' => '+92-300-3456789',
            'bio' => 'Social media marketing expert and graphic designer. Specializing in brand development, social media campaigns, and AI-powered graphic design. Helping businesses establish and grow their digital presence.',
            'profile_image_url' => '/images/profiles/ans/image.webp',
        ]);

        // Team member 4: Nazim Rauf (Technical Support - Team Member)
        User::create([
            'name' => 'Nazim Rauf',
            'email' => 'nazim@tasmiya.com',
            'password' => 'Password@123',
            'role' => 'team_member',
            'division_id' => 3, // Technical Support
            'is_active' => true,
            'phone' => '+92-300-4567890',
            'whatsapp_number' => '+92-300-4567890',
            'bio' => 'Technical installation and maintenance expert with extensive experience across the country. Specialized in CCTV systems, network security, solar installations, and intelligent building systems. Trusted by military and private contractors.',
            'profile_image_url' => '/images/profiles/nazim/image.webp',
        ]);

        echo "\n✅ 4 team members created successfully!\n";
        echo "Test Credentials:\n";
        echo "─────────────────────────────────────────\n";
        echo "Email:    atif@tasmiya.com\n";
        echo "Password: Password@123\n";
        echo "Role:     Admin\n";
        echo "─────────────────────────────────────────\n";
        echo "\nYou can also log in with the other team members:\n";
        echo "- waseem@tasmiya.com (Team Member)\n";
        echo "- ans@tasmiya.com (Team Member)\n";
        echo "- nazim@tasmiya.com (Team Member)\n";
        echo "\nAll passwords are: Password@123\n";
    }
}
