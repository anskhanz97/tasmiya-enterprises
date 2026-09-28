<?php

namespace App\Services;

use App\Models\SiteSetting;

class PaymentOptions
{
    public static function available(): array
    {
        $methods = [];
        foreach ([
            'bank_transfer' => ['Bank transfer', 'bank_iban', 'bank_account_title'],
            'raast' => ['Raast', 'raast_id', null],
            'easypaisa' => ['Easypaisa wallet', 'easypaisa_number', 'easypaisa_title'],
            'jazzcash' => ['JazzCash wallet', 'jazzcash_number', 'jazzcash_title'],
        ] as $key => [$label, $destination, $owner]) {
            if (SiteSetting::get('integration_enable_'.$key) !== '1' || ! SiteSetting::get('integration_'.$destination)) continue;
            $methods[$key] = [
                'label' => $label,
                'destination' => SiteSetting::get('integration_'.$destination),
                'owner' => $owner ? SiteSetting::get('integration_'.$owner) : null,
                'bank' => $key === 'bank_transfer' ? SiteSetting::get('integration_bank_name') : null,
                'mode' => 'manual',
            ];
        }
        $public = SiteSetting::get('integration_stripe_public') ?: config('services.stripe.public');
        $secret = SiteSetting::get('integration_stripe_secret') ?: config('services.stripe.secret');
        if (SiteSetting::get('integration_enable_card') === '1' && $public && $secret) {
            $methods['card'] = ['label' => 'Card', 'mode' => 'gateway', 'public_key' => $public];
        }
        return $methods;
    }
}
