<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    private const SECTIONS = [
        'contact' => ['company_email', 'company_whatsapp', 'company_address', 'map_embed_url'],
        'google' => ['google_client_id', 'google_api_key', 'google_app_id'],
        'onedrive' => ['onedrive_client_id'],
        'email' => ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_from_email'],
        'whatsapp' => ['whatsapp_business_account_id', 'whatsapp_phone_id', 'whatsapp_access_token', 'whatsapp_template_name', 'whatsapp_app_secret', 'whatsapp_verify_token'],
        'bank' => ['bank_name', 'bank_account_title', 'bank_iban', 'enable_bank_transfer'],
        'raast' => ['raast_id', 'enable_raast'],
        'easypaisa' => ['easypaisa_number', 'easypaisa_title', 'enable_easypaisa'],
        'jazzcash' => ['jazzcash_number', 'jazzcash_title', 'enable_jazzcash'],
        'stripe' => ['stripe_public', 'stripe_secret', 'stripe_webhook_secret', 'enable_card'],
    ];

    private const SECRETS = ['smtp_password', 'whatsapp_access_token', 'whatsapp_app_secret', 'whatsapp_verify_token', 'stripe_secret', 'stripe_webhook_secret'];

    private function requireAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function index()
    {
        $this->requireAdmin();
        $settings = [];
        foreach (self::SECTIONS as $fields) {
            foreach ($fields as $field) {
                $settings[$field] = SiteSetting::get('integration_'.$field);
            }
        }

        return view('settings.index', [
            'settings' => $settings,
            'socialLinks' => SiteSetting::getSocialLinks(),
            'googlePickerVerified' => SiteSetting::get('integration_google_verified_fingerprint') === self::googleFingerprint($settings),
        ]);
    }

    private static function googleFingerprint(array $settings): string
    {
        return hash('sha256', implode("\0", array_map(
            fn ($field) => (string) ($settings[$field] ?? ''),
            self::SECTIONS['google']
        )));
    }

    public function verifyGooglePicker(Request $request)
    {
        $this->requireAdmin();
        $validated = $request->validate([
            'file_id' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'access_token' => ['required', 'string', 'max:4096'],
        ]);

        $response = Http::timeout(8)->withToken($validated['access_token'])
            ->get('https://www.googleapis.com/drive/v3/files/'.$validated['file_id'], ['fields' => 'id,mimeType']);
        if (! $response->successful() || ! str_starts_with((string) $response->json('mimeType'), 'image/')) {
            return response()->json(['message' => 'Google Drive could not verify the imported image.'], 422);
        }

        $settings = [];
        foreach (self::SECTIONS['google'] as $field) {
            $settings[$field] = SiteSetting::get('integration_'.$field);
        }
        SiteSetting::set('integration_google_verified_fingerprint', self::googleFingerprint($settings));

        return response()->json(['verified' => true]);
    }

    public function updateIntegrations(Request $request)
    {
        $this->requireAdmin();
        $section = $request->input('section');
        abort_unless(is_string($section) && isset(self::SECTIONS[$section]), 422, 'Choose a settings section.');

        $rules = [
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_whatsapp' => ['nullable', 'regex:/^\+?[0-9\s()-]{7,25}$/'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'map_embed_url' => ['nullable', 'url', 'max:2000', function ($attribute, $value, $fail) {
                if ($value && ! preg_match('~^https://www\.google\.com/maps/embed\?~i', $value)) {
                    $fail('Use a Google Maps embed URL beginning with https://www.google.com/maps/embed?');
                }
            }],
            'google_client_id' => ['nullable', 'string', 'max:255', 'regex:/^[0-9]+-[a-z0-9]+\.apps\.googleusercontent\.com$/i'],
            'google_api_key' => ['nullable', 'string', 'max:255', 'regex:/^AIza[0-9A-Za-z_-]+$/'],
            'google_app_id' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'max:30'],
            'onedrive_client_id' => ['nullable', 'uuid'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'between:1,65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', Rule::in(['tls', 'ssl', 'none'])],
            'smtp_from_email' => ['nullable', 'email', 'max:255'],
            'whatsapp_phone_id' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'max:100'],
            'whatsapp_business_account_id' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'max:100'],
            'whatsapp_access_token' => ['nullable', 'string', 'max:2000'],
            'whatsapp_template_name' => ['nullable', 'alpha_dash', 'max:255'],
            'whatsapp_app_secret' => ['nullable', 'string', 'max:255'],
            'whatsapp_verify_token' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'bank_account_title' => ['nullable', 'string', 'max:120'],
            'bank_iban' => ['nullable', 'regex:/^PK[0-9A-Z]{22}$/i'],
            'raast_id' => ['nullable', 'string', 'max:120'],
            'easypaisa_number' => ['nullable', 'regex:/^[0-9+\s-]{10,25}$/'],
            'easypaisa_title' => ['nullable', 'string', 'max:120'],
            'jazzcash_number' => ['nullable', 'regex:/^[0-9+\s-]{10,25}$/'],
            'jazzcash_title' => ['nullable', 'string', 'max:120'],
            'enable_bank_transfer' => ['nullable', 'boolean'],
            'enable_raast' => ['nullable', 'boolean'],
            'enable_easypaisa' => ['nullable', 'boolean'],
            'enable_jazzcash' => ['nullable', 'boolean'],
            'enable_card' => ['nullable', 'boolean'],
            'stripe_public' => ['nullable', 'string', 'max:255'],
            'stripe_secret' => ['nullable', 'string', 'max:255'],
            'stripe_webhook_secret' => ['nullable', 'string', 'max:255'],
        ];

        $sectionRules = array_intersect_key($rules, array_flip(self::SECTIONS[$section]));
        $sectionRules['clear_secrets'] = ['nullable', 'array'];
        $sectionRules['clear_secrets.*'] = ['string', Rule::in(array_intersect(self::SECRETS, self::SECTIONS[$section]))];
        $validated = $request->validate($sectionRules);
        $toClear = $validated['clear_secrets'] ?? [];

        foreach (self::SECTIONS[$section] as $field) {
            if (str_starts_with($field, 'enable_')) {
                SiteSetting::set('integration_'.$field, $request->boolean($field) ? '1' : '0', 'boolean');
                continue;
            }
            if (in_array($field, self::SECRETS, true)) {
                if (in_array($field, $toClear, true)) {
                    SiteSetting::set('integration_'.$field, null, 'secret');
                } elseif (filled($validated[$field] ?? null)) {
                    SiteSetting::set('integration_'.$field, $validated[$field], 'secret');
                }
                continue;
            }
            SiteSetting::set('integration_'.$field, $validated[$field] ?? null, 'text');
        }

        return redirect(route('settings.index').'#ig-'.$section)->with('success', ucfirst($section).' settings saved.');
    }

    public function updateSocialLinks(Request $request)
    {
        $this->requireAdmin();
        $validated = $request->validate([
            'links' => ['nullable', 'array', 'max:20'],
            'links.*.label' => ['required', 'string', 'max:35'],
            'links.*.url' => ['required', 'url', 'max:500', 'regex:/^https:\/\//i'],
        ]);
        $links = collect($validated['links'] ?? [])->map(fn ($link) => [
            'label' => trim($link['label']),
            'url' => $link['url'],
        ])->values()->all();
        SiteSetting::set('social_links', json_encode($links, JSON_UNESCAPED_SLASHES), 'json');

        return redirect(route('settings.index').'#ig-social')->with('success', 'Social links saved.');
    }

    public function whatsappTemplates()
    {
        $this->requireAdmin();
        $accountId = SiteSetting::get('integration_whatsapp_business_account_id');
        $token = SiteSetting::get('integration_whatsapp_access_token');
        if (! $accountId || ! $token) {
            return response()->json(['message' => 'Save the Meta business account ID and access token first.'], 422);
        }
        $response = Http::timeout(8)->withToken($token)->get("https://graph.facebook.com/v23.0/{$accountId}/message_templates", [
            'fields' => 'name,status,language',
            'limit' => 100,
        ]);
        if (! $response->successful()) {
            return response()->json(['message' => 'Meta could not load templates. Check the account ID, token, and template permissions.'], 502);
        }
        return response()->json(['templates' => collect($response->json('data', []))
            ->filter(fn ($template) => ($template['status'] ?? '') === 'APPROVED')
            ->map(fn ($template) => ['name' => $template['name'], 'language' => $template['language'] ?? ''])
            ->values()]);
    }
}
