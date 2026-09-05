<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Profile;
use App\Models\Division;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Get divisions
        $fbrDivision = Division::where('slug', 'fbr')->first();
        $itDivision = Division::where('slug', 'it')->first();
        $techDivision = Division::where('slug', 'tech')->first();

        // Get profiles by user name
        $atifProfile = Profile::whereHas('user', fn($q) => $q->where('name', 'like', '%Atif%'))->first();
        $wazeemProfile = Profile::whereHas('user', fn($q) => $q->where('name', 'like', '%Waseem%'))->first();
        $ansProfile = Profile::whereHas('user', fn($q) => $q->where('name', 'like', '%Ans%'))->first();
        $nazimProfile = Profile::whereHas('user', fn($q) => $q->where('name', 'like', '%Nazim%'))->first();

        $projects = [
            // Atif Safdar - FBR Taxation (2 projects)
            [
                'title' => 'Tax Compliance Audit',
                'description' => 'Comprehensive tax filing and compliance audit for a leading manufacturing company, ensuring regulatory adherence and optimal tax planning.',
                'profile_id' => $atifProfile?->id,
                'division_id' => $fbrDivision?->id,
                'icon' => '📊',
                'is_featured' => true,
                'display_order' => 1,
            ],
            [
                'title' => 'Freelancer Tax Planning',
                'description' => 'Strategic tax planning and filer registration for 50+ freelance professionals, maximizing deductions and ensuring full compliance.',
                'profile_id' => $atifProfile?->id,
                'division_id' => $fbrDivision?->id,
                'icon' => '💰',
                'is_featured' => true,
                'display_order' => 2,
            ],

            // Waseem Asghar - IT Services (2 projects)
            [
                'title' => 'Custom Web Platform',
                'description' => 'Built a comprehensive e-commerce platform with payment gateway integration, user dashboard, and admin panel for a mid-size retailer.',
                'profile_id' => $wazeemProfile?->id,
                'division_id' => $itDivision?->id,
                'icon' => '💻',
                'is_featured' => true,
                'display_order' => 3,
            ],
            [
                'title' => 'Digital Marketing Campaign',
                'description' => 'Executed multi-channel digital marketing campaign resulting in 300% ROI increase for a local service business.',
                'profile_id' => $wazeemProfile?->id,
                'division_id' => $itDivision?->id,
                'icon' => '📱',
                'is_featured' => true,
                'display_order' => 4,
            ],

            // Ans Khan - IT Services (2 projects)
            [
                'title' => 'Mobile App Development',
                'description' => 'Developed cross-platform mobile application with real-time features, push notifications, and cloud synchronization for a fitness startup.',
                'profile_id' => $ansProfile?->id,
                'division_id' => $itDivision?->id,
                'icon' => '📲',
                'is_featured' => true,
                'display_order' => 5,
            ],
            [
                'title' => 'Cloud Infrastructure Setup',
                'description' => 'Designed and deployed scalable cloud architecture on AWS with auto-scaling, load balancing, and disaster recovery for enterprise client.',
                'profile_id' => $ansProfile?->id,
                'division_id' => $itDivision?->id,
                'icon' => '☁️',
                'is_featured' => true,
                'display_order' => 6,
            ],

            // Nazim Rauf - Technical Support (2 projects)
            [
                'title' => 'Security System Installation',
                'description' => 'Complete CCTV and access control system installation for a corporate headquarters including network configuration and staff training.',
                'profile_id' => $nazimProfile?->id,
                'division_id' => $techDivision?->id,
                'icon' => '🔒',
                'is_featured' => true,
                'display_order' => 7,
            ],
            [
                'title' => 'Network Infrastructure',
                'description' => 'Designed and installed enterprise-grade network infrastructure for a growing tech startup with redundancy and failover systems.',
                'profile_id' => $nazimProfile?->id,
                'division_id' => $techDivision?->id,
                'icon' => '🌐',
                'is_featured' => true,
                'display_order' => 8,
            ],
        ];

        foreach ($projects as $projectData) {
            if ($projectData['profile_id'] && $projectData['division_id']) {
                Project::create($projectData);
            }
        }
    }
}