<?php

namespace App\Enums;

enum SpamProtection: string
{
    case None = 'none';
    case Honeypot = 'honeypot';
    case Recaptcha = 'recaptcha';
    case Turnstile = 'turnstile';

    public function label(): string
    {
        return match ($this) {
            self::None => 'None',
            self::Honeypot => 'Honeypot + Rate Limiting',
            self::Recaptcha => 'Google reCAPTCHA v3',
            self::Turnstile => 'Cloudflare Turnstile',
        };
    }
}
