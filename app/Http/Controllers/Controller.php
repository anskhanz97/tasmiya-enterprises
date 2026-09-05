<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

/**
 * Base Controller
 * 
 * All controllers in this application extend this class.
 * Provides access to common methods like authorize(), validate(), auth(), etc.
 * 
 * Methods available:
 * - authorize() - Check authorization policies
 * - validate() - Validate request data
 * - auth() - Access the authentication facade
 * - $this->user() - Get the authenticated user
 * 
 * Example:
 * $this->authorize('delete', $post);
 * $this->validate($request, ['name' => 'required']);
 * $user = $this->user();
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get the currently authenticated user
     * 
     * Convenience method to access auth()->user()
     * 
     * @return \App\Models\User|null
     */
    protected function user()
    {
        return Auth::user();
    }

    /**
     * Check if user is authenticated
     * 
     * Convenience method to access auth()->check()
     * 
     * @return bool
     */
    protected function check(): bool
    {
        return Auth::check();
    }
}
