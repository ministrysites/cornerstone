<?php

declare(strict_types=1);

use App\Enums\EmailCategory;
use App\Services\EmailAddressClassifier;

test('personal addresses are classified as personal', function (): void {
    $result = (new EmailAddressClassifier())->classify('reader@example.com');

    expect($result->category)->toBe(EmailCategory::Personal);
    expect($result->domain)->toBe('example.com');
    expect($result->email)->toBe('reader@example.com');
});

test('role based local parts are classified as role', function (): void {
    expect((new EmailAddressClassifier())->classify('admin@example.com')->category)
        ->toBe(EmailCategory::Role);

    expect((new EmailAddressClassifier())->classify('support@acme.test')->category)
        ->toBe(EmailCategory::Role);
});

test('disposable domains are classified as disposable', function (): void {
    expect((new EmailAddressClassifier())->classify('whatever@mailinator.com')->category)
        ->toBe(EmailCategory::Disposable);
});

test('classification is case insensitive', function (): void {
    $result = (new EmailAddressClassifier())->classify('ADMIN@Example.COM');

    expect($result->category)->toBe(EmailCategory::Role);
    expect($result->domain)->toBe('example.com');
});
