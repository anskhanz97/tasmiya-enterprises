<?php

namespace App\Services;

use App\Models\ContactInquiry;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * WhatsApp Business API base URL
     */
    private const API_BASE_URL = 'https://graph.facebook.com/v23.0';

    /**
     * Business phone number ID (from .env)
     */
    private string $phoneNumberId;

    /**
     * Business access token (from .env)
     */
    private string $accessToken;

    /**
     * Constructor - Initialize with config values
     */
    public function __construct()
    {
        $this->phoneNumberId = SiteSetting::get('integration_whatsapp_phone_id') ?: (config('services.whatsapp.business_phone_id') ?: '');
        $this->accessToken = SiteSetting::get('integration_whatsapp_access_token') ?: (config('services.whatsapp.api_token') ?: '');
    }

    /**
     * Send a WhatsApp message to a phone number
     * 
     * @param string $toNumber Phone number with country code (e.g., +923001234567)
     * @param string $message Message text
     * @param string|null $templateName Name of pre-approved template
     * @param array $templateParameters Template variables
     * @return bool Success status
     */
    public function sendMessage(
        string $toNumber,
        string $message,
        ?string $templateName = null,
        array $templateParameters = []
    ): bool {
        if (!$this->isConfigured()) {
            Log::warning('WhatsApp service not configured');
            return false;
        }

        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $this->formatPhoneNumber($toNumber),
            ];

            if ($templateName) {
                $payload['type'] = 'template';
                $payload['template'] = [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'en_US',
                    ],
                ];
                
                if (!empty($templateParameters)) {
                    $payload['template']['components'] = [
                        [
                            'type' => 'body',
                            'parameters' => $templateParameters,
                        ],
                    ];
                }
            } else {
                $payload['type'] = 'text';
                $payload['text'] = [
                    'body' => $message,
                ];
            }

            $response = Http::timeout(8)->withToken($this->accessToken)
                ->post(
                    self::API_BASE_URL . "/{$this->phoneNumberId}/messages",
                    $payload
                );

            /** @var \Illuminate\Http\Client\Response $response */
            $responseData = $response->json() ?? [];

            if ($response->status() === 200) {
                Log::info('WhatsApp message sent', [
                    'to' => $toNumber,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                ]);
                return true;
            }

            Log::error('WhatsApp message failed', [
                'to' => $toNumber,
                'response' => $responseData,
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp message exception', [
                'to' => $toNumber,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send message to user (from User model)
     * 
     * @param mixed $user User model or user ID
     * @param string $message Message text
     * @return bool
     */
    public function sendToUser($user, string $message): bool
    {
        $phoneNumber = $user->whatsapp_number ?? $user->phone;

        if (!$phoneNumber) {
            Log::warning('User has no WhatsApp number', ['user_id' => $user->id]);
            return false;
        }

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Handle incoming WhatsApp webhook message
     * Called by webhook controller
     */
    public function handleIncomingMessage(array $message): void
    {
        try {
            $phoneNumber = $message['from'] ?? null;
            $messageBody = $message['text']['body'] ?? null;
            $messageType = $message['type'] ?? 'text';

            if (!$phoneNumber || !$messageBody) {
                Log::warning('Invalid WhatsApp message format', $message);
                return;
            }

            // Create contact inquiry from WhatsApp message
            $inquiry = ContactInquiry::create([
                'phone' => $phoneNumber,
                'message' => $messageBody,
                'source' => 'whatsapp',
                'source_type' => $messageType,
            ]);

            Log::info('WhatsApp inquiry created', [
                'inquiry_id' => $inquiry->id,
                'phone' => $phoneNumber,
            ]);

            // Send acknowledgment
            $this->sendMessage(
                $phoneNumber,
                'Thank you for reaching out! We will respond to your inquiry shortly.'
            );
        } catch (\Exception $e) {
            Log::error('Failed to handle WhatsApp message', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verify webhook signature (for security)
     * 
     * @param string $payload Request payload
     * @param string $signature Signature header value
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $appSecret = SiteSetting::get('integration_whatsapp_app_secret') ?: config('services.whatsapp.app_secret');
        if (! $appSecret || ! str_starts_with($signature, 'sha256=')) {
            return false;
        }

        $hash = hash_hmac(
            'sha256',
            $payload,
            $appSecret
        );

        return hash_equals("sha256={$hash}", $signature);
    }

    /**
     * Format phone number to WhatsApp format
     * Removes spaces, dashes, and ensures it starts with +
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Ensure it starts with +
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    /**
     * Check if WhatsApp service is properly configured
     */
    private function isConfigured(): bool
    {
        return !empty($this->phoneNumberId) && !empty($this->accessToken);
    }

    public function notifyCompanyAboutInquiry(ContactInquiry $inquiry): bool
    {
        $number = SiteSetting::get('integration_company_whatsapp');
        $template = SiteSetting::get('integration_whatsapp_template_name');
        if (! $number || ! $template || ! $this->isConfigured()) return false;

        $summary = "{$inquiry->name}: {$inquiry->subject} (inquiry #{$inquiry->id})";
        return $this->sendMessage($number, $summary, $template, [
            ['type' => 'text', 'text' => mb_substr($summary, 0, 500)],
        ]);
    }

    /**
     * Send notification to admins about inquiry
     */
    public function notifyAdminsAboutInquiry(ContactInquiry $inquiry): bool
    {
        try {
            $message = "New inquiry from {$inquiry->phone}:\n{$inquiry->message}";
            
            // Get all admin WhatsApp numbers
            $admins = \App\Models\User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                if ($admin->whatsapp_number) {
                    $this->sendToUser($admin, $message);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to notify admins', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
