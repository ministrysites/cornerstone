<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\EmailCategory;

readonly class EmailClassification
{
    public function __construct(
        public string $email,
        public string $domain,
        public EmailCategory $category,
    ) {}
}
