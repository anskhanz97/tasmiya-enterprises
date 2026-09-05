<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Profile;
use App\Models\Division;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\Request;

/**
 * ProjectController - Manage portfolio projects
 * 
 * Handles CRUD operations for projects/portfolio items
 * - Public: View portfolio gallery and project details
 * - Admin: Create, edit, delete projects with image uploads
 */
class ProjectController extends Controller
{
    /**
     * Display portfolio gallery (public view)
     * 
     * GET /portfolio
     * Shows all projects in masonry grid with division filtering
     */
    public function index(Request $request): View
    {
        // Get filter parameters
        $divisionFilter = $request->get('division');
        
        // Build query with eager loading
        $query = Project::with(['division', 'profile.user'])
            ->ordered();
        
        // Apply division filter if specified
        if ($divisionFilter) {
            $query->where('division_id', $divisionFilter);
        }
        
        $projects = $query->get();
        
        // Get featured projects for top section
        $featuredProjects = Project::with(['division', 'profile.user'])
            ->featured()
            ->ordered()
            ->limit(6)
            ->get();
        
        // Get all divisions for filter tabs
        $divisions = Division::all();
        
        return view('projects.index', [
            'projects' => $projects,
            'featuredProjects' => $featuredProjects,
            'divisions' => $divisions,
            'currentDivision' => $divisionFilter,
        ]);
    }

    /**
     * Display single project detail
     * 
     * GET /projects/{project}
     * Shows full project information
     */
    public function show(Project $project): View
    {
        // Eager load relationships
        $project->load(['division', 'profile.user']);
        
        return view('projects.show', [
            'project' => $project,
        ]);
    }

    /**
     * Show form to create new project (admin only)
     * 
     * GET /admin/projects/create
     */
    public function create(): View
    {
        $this->authorize('create', Project::class);
        
        $profiles = Profile::with('user')->get();
        $divisions = Division::all();
        
        return view('projects.create', [
            'profiles' => $profiles,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Store new project (admin only)
     * 
     * POST /admin/projects
     * Handles image upload and project creation
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);
        
        $validated = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('project-images', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }
        
        // Create the project
        $project = Project::create($validated);
        
        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    /**
     * Show form to edit project (admin only)
     * 
     * GET /admin/projects/{project}/edit
     */
    public function edit(Project $project): View
    {
        $this->authorize('update', $project);
        
        $profiles = Profile::with('user')->get();
        $divisions = Division::all();
        
        return view('projects.edit', [
            'project' => $project,
            'profiles' => $profiles,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Update project (admin only)
     * 
     * PUT /admin/projects/{project}
     * Handles image replacement
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        
        $validated = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($project->image_url) {
                $oldPath = str_replace('/storage/', '', parse_url($project->image_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }
            
            // Store new image
            $path = $request->file('image')->store('project-images', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }
        
        // Update the project
        $project->update($validated);
        
        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Delete project (admin only)
     * 
     * DELETE /admin/projects/{project}
     * Removes project and associated image
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        
        // Delete image if exists
        if ($project->image_url) {
            $oldPath = str_replace('/storage/', '', parse_url($project->image_url, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
        }
        
        $project->delete();
        
        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }
}
