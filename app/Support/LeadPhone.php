<?php

namespace App\Support;

class LeadPhone
{
    public static function normalize(?string $phone): ?string
    {
        if (! filled($phone)) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (preg_match('/^03\d{9}$/', $digits)) {
            $digits = '92'.substr($digits, 1);
        } elseif (preg_match('/^3\d{9}$/', $digits)) {
            $digits = '92'.$digits;
        }

        return $digits === '' ? null : '+'.$digits;
    }
}
