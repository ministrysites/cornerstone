<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\EmailClassification;
use App\Enums\EmailCategory;

class EmailAddressClassifier
{
    private const ROLE_LOCAL_PARTS = [
        'admin',
        'administrator',
        'billing',
        'contact',
        'help',
        'info',
        'noreply',
        'no-reply',
        'postmaster',
        'sales',
        'support',
        'webmaster',
    ];

    private const DISPOSABLE_DOMAINS = [
        '10minutemail.com',
        'guerrillamail.com',
        'mailinator.com',
        'tempmail.com',
        'trashmail.com',
        'yopmail.com',
    ];

    public function classify(string $email): EmailClassification
    {
        $normalized = mb_strtolower($email);
        [$localPart, $domain] = explode('@', $normalized);

        $category = match (true) {
            in_array($domain, self::DISPOSABLE_DOMAINS, true) => EmailCategory::Disposable,
            in_array($localPart, self::ROLE_LOCAL_PARTS, true) => EmailCategory::Role,
            default => EmailCategory::Personal,
        };

        return new EmailClassification(
            email: $email,
            domain: $domain,
            category: $category,
        );
    }
}
