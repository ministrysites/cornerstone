<?php

declare(strict_types=1);

namespace App\Enums;

enum EmailCategory: string
{
    case Personal = 'personal';
    case Role = 'role';
    case Disposable = 'disposable';
}
