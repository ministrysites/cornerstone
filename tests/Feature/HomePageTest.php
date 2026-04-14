<?php

declare(strict_types=1);

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

test('selecting an out of range pillar is clamped to a valid index', function (): void {
    Livewire::test(HomePage::class)
        ->call('selectPillar', 99)
        ->assertSet('activePillar', 3)
        ->call('selectPillar', -10)
        ->assertSet('activePillar', 0);
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

test('the validation demo marks the component as validated on success', function (): void {
    Livewire::test(HomePage::class)
        ->set('email', 'reader@example.com')
        ->call('runDemo')
        ->assertHasNoErrors()
        ->assertSet('validated', true)
        ->assertSet('email', '')
        ->assertSee('Round-trip complete');
});
