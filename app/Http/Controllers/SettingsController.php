<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Show the settings management page
     */
    public function index()
    {
        $socialLinks = SiteSetting::getSocialLinks();
        
        return view('settings.index', compact('socialLinks'));
    }

    /**
     * Update social media links
     */
    public function updateSocialLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update each social link
        SiteSetting::set('social_facebook', $request->facebook, 'url', 'Facebook page URL');
        SiteSetting::set('social_twitter', $request->twitter, 'url', 'Twitter/X profile URL');
        SiteSetting::set('social_linkedin', $request->linkedin, 'url', 'LinkedIn company page URL');
        SiteSetting::set('social_instagram', $request->instagram, 'url', 'Instagram profile URL');

        return redirect()
            ->back()
            ->with('success', 'Social media links updated successfully! 🎉');
    }
}
