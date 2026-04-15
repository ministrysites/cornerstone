<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Cornerstone — The Laravel Livewire Starter Kit')]
class HomePage extends Component
{
    private const PILLARS = [
        [
            'number' => '01',
            'label' => 'Typed',
            'title' => 'Strict types on every line.',
            'body' => 'Every PHP file declares strict types, properties and methods are explicitly typed, and public state stays narrow. The result is code that reads like documentation — and AI tooling that mimics the pattern correctly on the first try.',
        ],
        [
            'number' => '02',
            'label' => 'Reactive',
            'title' => 'Livewire-first interfaces.',
            'body' => 'Server-driven components handle interactive flows while Alpine covers small client-side niceties. No SPA build pipeline, no duplicated state — just Blade, Livewire, and a measured dash of Alpine where it belongs.',
        ],
        [
            'number' => '03',
            'label' => 'Tested',
            'title' => 'Pest from the very first commit.',
            'body' => 'The kit ships with Pest 4, architecture tests, and sensible defaults. "Tests pass" means more than "the files parse" — it means the boundaries the project cares about are still honored.',
        ],
        [
            'number' => '04',
            'label' => 'Analyzed',
            'title' => 'PHPStan before merge.',
            'body' => 'Larastan is pre-configured, Pint keeps style consistent, and composer check runs the full quality gate locally and in CI so pull requests stay honest before they land on a protected branch.',
        ],
    ];

    private const FAQS = [
        [
            'q' => 'What PHP version does Cornerstone require?',
            'a' => 'Cornerstone targets PHP 8.3 and newer. The starter assumes a modern PHP baseline so new projects can use current language features without carrying legacy compatibility baggage.',
        ],
        [
            'q' => 'Is authentication included?',
            'a' => 'No. Cornerstone is a starter kit, not a full product scaffold. Authentication is left to the consuming project so teams can choose the approach that actually fits their application.',
        ],
        [
            'q' => 'Can I swap Livewire for Inertia.js?',
            'a' => 'Not really. Cornerstone is intentionally built around Livewire for web UI, with Alpine covering lightweight local interactions. If your default direction is Inertia, a purpose-built Inertia starter will be a better fit.',
        ],
        [
            'q' => 'How do I run the quality checks?',
            'a' => 'Run composer check to execute the formatting, static analysis, and test suite together. If you want to fix style issues first, run composer fix before rerunning the checks.',
        ],
        [
            'q' => 'Is this production-ready?',
            'a' => 'The starter is meant to be a strong base, not a finished application. Production readiness depends on the project you build on top of it, but the included tooling is there to help teams maintain quality as the app grows.',
        ],
    ];

    #[Locked]
    public int $activePillar = 0;

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Locked]
    public bool $validated = false;

    public function selectPillar(int $index): void
    {
        $this->activePillar = $index;
    }

    public function runDemo(): void
    {
        $this->validate();

        $this->validated = true;
        $this->email = '';
    }

    public function resetDemo(): void
    {
        $this->validated = false;
        $this->email = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.home-page', [
            'pillars' => self::PILLARS,
            'faqs' => self::FAQS,
        ]);
    }
}
