<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Profile;
use App\Models\Service;
use App\Models\Project;
use Illuminate\View\View;

/**
 * DivisionController - Handle division-related pages
 * 
 * Manages division-specific landing pages with their team, services, and projects
 */
class DivisionController extends Controller
{
    /**
     * Display all divisions overview
     * 
     * GET /divisions
     * Shows all three divisions as cards
     */
    public function index(): View
    {
        $divisions = Division::all();
        
        // Get counts for each division
        foreach ($divisions as $division) {
            $division->team_count = Profile::whereHas('user', function($query) use ($division) {
                $query->where('division_id', $division->id);
            })->count();
            
            $division->service_count = Service::where('division_id', $division->id)->count();
            $division->project_count = Project::where('division_id', $division->id)->count();
        }
        
        return view('divisions.index', [
            'divisions' => $divisions,
        ]);
    }

    /**
     * Display single division landing page
     * 
     * GET /divisions/{division:slug}
     * Shows division details, team members, services, and projects
     */
    public function show(Division $division): View
    {
        // Get team members in this division
        $teamMembers = Profile::with('user')
            ->whereHas('user', function($query) use ($division) {
                $query->where('division_id', $division->id);
            })
            ->visible()
            ->get();
        
        // Get services offered by this division
        $services = Service::where('division_id', $division->id)
            ->with(['profiles.user'])
            ->get();
        
        // Get projects from this division
        $projects = Project::where('division_id', $division->id)
            ->with(['profile.user'])
            ->ordered()
            ->limit(6)
            ->get();
        
        return view('divisions.show', [
            'division' => $division,
            'teamMembers' => $teamMembers,
            'services' => $services,
            'projects' => $projects,
        ]);
    }
}
