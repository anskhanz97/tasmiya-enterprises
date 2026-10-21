<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Mail;

class LeadMailConfiguration
{
    public function configure(): ?array
    {
        $host = SiteSetting::get('integration_smtp_host');
        $from = SiteSetting::get('integration_smtp_from_email');
        if ($host && filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $encryption = SiteSetting::get('integration_smtp_encryption') ?: 'tls';
            config(['mail.mailers.lead_reminders' => [
                'transport' => 'smtp', 'scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
                'host' => $host, 'port' => (int) (SiteSetting::get('integration_smtp_port') ?: 587),
                'username' => SiteSetting::get('integration_smtp_username'),
                'password' => SiteSetting::get('integration_smtp_password'), 'timeout' => 8,
                'auto_tls' => $encryption !== 'none',
                'local_domain' => parse_url(config('app.url'), PHP_URL_HOST),
            ]]);
            Mail::purge('lead_reminders');

            return ['mailer' => 'lead_reminders', 'from' => $from, 'name' => 'Tasmiya Enterprises'];
        }
        // A log/array mailer must never be reported as a delivered email.
        if (config('mail.default') === 'smtp' && config('mail.mailers.smtp.host')
            && filter_var(config('mail.from.address'), FILTER_VALIDATE_EMAIL)) {
            return ['mailer' => 'smtp', 'from' => config('mail.from.address'), 'name' => config('mail.from.name') ?: 'Tasmiya Enterprises'];
        }

        return null;
    }
}
