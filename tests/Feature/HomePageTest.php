<?php

declare(strict_types=1);

use App\Enums\EmailCategory;
use App\Livewire\HomePage;
use Livewire\Livewire;

use function Pest\Laravel\get;

test('the homepage renders successfully', function (): void {
    get('/')
        ->assertOk()
        ->assertSeeLivewire(HomePage::class)
        ->assertSee('A deliberate starting line')
        ->assertSee('The Cornerstone')
        ->assertSee('Frequently asked questions');
});

test('the homepage sets a custom page title', function (): void {
    get('/')
        ->assertOk()
        ->assertSee('Cornerstone — The Laravel Livewire Starter Kit', false);
});

test('faq questions and answers render on the homepage', function (): void {
    get('/')
        ->assertOk()
        ->assertSee('What PHP version does Cornerstone require?')
        ->assertSee('Cornerstone targets PHP 8.3 and newer.')
        ->assertSee('Is authentication included?');
});

test('all four pillars render on the homepage', function (): void {
    Livewire::test(HomePage::class)
        ->assertSee('Typed')
        ->assertSee('Reactive')
        ->assertSee('Tested')
        ->assertSee('Analyzed')
        ->assertSet('activePillar', 0);
});

test('selecting a pillar updates the active pillar via livewire', function (): void {
    Livewire::test(HomePage::class)
        ->assertSet('activePillar', 0)
        ->call('selectPillar', 2)
        ->assertSet('activePillar', 2)
        ->assertSee('Pest from the very first commit.');
});

test('the validation demo rejects an empty value', function (): void {
    Livewire::test(HomePage::class)
        ->set('email', '')
        ->call('runDemo')
        ->assertHasErrors(['email' => 'required'])
        ->assertSet('validated', false);
});

test('the validation demo rejects an invalid value', function (): void {
    Livewire::test(HomePage::class)
        ->set('email', 'not-a-real-email')
        ->call('runDemo')
        ->assertHasErrors(['email' => 'email'])
        ->assertSet('validated', false);
});

test('the validation demo classifies a successful submission', function (): void {
    Livewire::test(HomePage::class)
        ->set('email', 'reader@example.com')
        ->call('runDemo')
        ->assertHasNoErrors()
        ->assertSet('validated', true)
        ->assertSet('lastCategory', EmailCategory::Personal)
        ->assertSet('lastDomain', 'example.com')
        ->assertSee('Classified as personal');
});

test('role based submissions are surfaced through the classifier', function (): void {
    Livewire::test(HomePage::class)
        ->set('email', 'support@example.com')
        ->call('runDemo')
        ->assertSet('lastCategory', EmailCategory::Role)
        ->assertSee('Classified as role');
});

test('the validation demo can be reset and run again', function (): void {
    Livewire::test(HomePage::class)
        ->set('email', 'reader@example.com')
        ->call('runDemo')
        ->assertSet('validated', true)
        ->call('resetDemo')
        ->assertSet('validated', false)
        ->assertSet('email', '')
        ->assertSet('lastCategory', null)
        ->assertSet('lastDomain', null)
        ->assertHasNoErrors();
});
