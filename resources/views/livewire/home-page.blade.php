<div class="min-h-screen bg-carbon-50 font-sans text-carbon-900 antialiased">
    <header class="border-b border-carbon-900">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
            <a href="/" class="font-display text-2xl font-black tracking-tight">
                Cornerstone<span class="text-ember-600">.</span>
            </a>
            <a
                href="https://github.com/ministrysites/cornerstone"
                target="_blank"
                rel="noopener noreferrer"
                class="border border-carbon-900 px-4 py-2 text-[11px] uppercase tracking-[0.22em] transition-colors hover:bg-carbon-900 hover:text-carbon-50"
            >
                GitHub ↗
            </a>
        </div>
    </header>

    <section class="border-b border-carbon-900">
        <div class="mx-auto max-w-5xl px-6 py-20">
            <p class="text-[11px] uppercase tracking-[0.25em] text-ember-600">A deliberate starting line</p>
            <h1 class="mt-4 font-display text-5xl font-black leading-[0.9] tracking-tight md:text-7xl">
                The Cornerstone<br>starter kit.
            </h1>
            <p class="mt-8 max-w-2xl text-base leading-relaxed text-carbon-700">
                Laravel 13, Livewire 4, Alpine, Tailwind 4, Pest, and PHPStan — wired in with opinions already applied so new projects begin with conventions instead of drift.
            </p>
        </div>
    </section>

    <section id="pillars" class="border-b border-carbon-900">
        <div class="mx-auto max-w-5xl px-6 py-16">
            <p class="text-[11px] uppercase tracking-[0.25em] text-ember-600">§ Pillars</p>
            <h2 class="mt-3 font-display text-4xl font-black tracking-tight md:text-5xl">
                What the kit insists upon.
            </h2>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-carbon-700">
                Click a pillar. The panel below re-renders on the server via Livewire — no page reload, no duplicated state.
            </p>

            <div class="mt-10 grid grid-cols-12 border border-carbon-900">
                <div class="col-span-12 md:col-span-5 md:border-r md:border-carbon-900">
                    @foreach ($pillars as $i => $pillar)
                        <button
                            wire:key="pillar-{{ $pillar['number'] }}"
                            type="button"
                            wire:click="selectPillar({{ $i }})"
                            @class([
                                'flex w-full items-baseline gap-5 border-b border-carbon-900/25 px-6 py-5 text-left transition-colors last:border-b-0',
                                'bg-carbon-900 text-carbon-50' => $activePillar === $i,
                                'hover:bg-carbon-100' => $activePillar !== $i,
                            ])
                            aria-pressed="{{ $activePillar === $i ? 'true' : 'false' }}"
                        >
                            <span class="font-mono text-xs tabular-nums">{{ $pillar['number'] }}</span>
                            <span class="font-display text-2xl font-black tracking-tight">{{ $pillar['label'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="col-span-12 md:col-span-7">
                    <div wire:key="pillar-panel-{{ $activePillar }}" class="px-6 py-10 md:px-10">
                        <p class="font-mono text-[11px] uppercase tracking-[0.3em] text-ember-600">
                            Pillar №{{ $pillars[$activePillar]['number'] }}
                        </p>
                        <h3 class="mt-4 font-display text-3xl font-black leading-tight tracking-tight md:text-4xl">
                            {{ $pillars[$activePillar]['title'] }}
                        </h3>
                        <p class="mt-5 text-sm leading-relaxed text-carbon-700">
                            {{ $pillars[$activePillar]['body'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="border-b border-carbon-900">
        <div class="mx-auto max-w-5xl px-6 py-16">
            <p class="text-[11px] uppercase tracking-[0.25em] text-ember-600">§ Frequently asked questions</p>
            <h2 class="mt-3 font-display text-4xl font-black tracking-tight md:text-5xl">
                Questions.
            </h2>

            <div class="mt-10 border-y-[3px] border-carbon-900">
                @foreach ($faqs as $index => $faq)
                    <x-cornerstone.faq-item
                        :number="$index + 1"
                        :question="$faq['q']"
                        :open="$index === 0"
                    >
                        {{ $faq['a'] }}
                    </x-cornerstone.faq-item>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-b border-carbon-900 bg-carbon-900 text-carbon-50">
        <div class="mx-auto max-w-5xl px-6 py-16">
            <p class="text-[11px] uppercase tracking-[0.25em] text-ember-400">§ Playground</p>
            <h2 class="mt-3 font-display text-4xl font-black tracking-tight md:text-5xl">
                Server-side validation, live.
            </h2>
            <p class="mt-5 max-w-xl text-sm leading-relaxed text-carbon-300">
                Type anything. Livewire round-trips the value, runs the same validation rules it would in production, and re-renders this panel. A demo of <span class="font-mono text-carbon-50">#[Validate]</span> and <span class="font-mono text-carbon-50">wire:submit</span> — nothing is stored.
            </p>

            <div class="mt-10 max-w-xl">
                @if ($validated)
                    <div class="border border-ember-400 p-6">
                        <p class="font-mono text-[11px] uppercase tracking-[0.25em] text-ember-400">Round-trip complete ✓</p>
                        <p class="mt-3 text-sm leading-relaxed text-carbon-300">
                            The value passed Laravel's email rule and this panel re-rendered. That's the whole demo.
                        </p>
                    </div>
                @else
                    <form wire:submit="runDemo" class="space-y-4" novalidate>
                        <label for="email" class="block font-mono text-[11px] uppercase tracking-[0.25em] text-ember-400">
                            Any value — we'll validate it
                        </label>
                        <input
                            id="email"
                            type="text"
                            wire:model="email"
                            placeholder="try something@invalid"
                            autocomplete="off"
                            @class([
                                'w-full border-b-2 bg-transparent pb-2 font-display text-2xl text-carbon-50 placeholder:text-carbon-600 focus:outline-none',
                                'border-ember-400' => ! $errors->has('email'),
                                'border-red-400' => $errors->has('email'),
                            ])
                        />
                        @error('email')
                            <p class="font-mono text-[11px] uppercase tracking-[0.25em] text-red-400">{{ $message }}</p>
                        @enderror

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="runDemo"
                            class="inline-flex items-center gap-3 border border-ember-400 bg-ember-400 px-5 py-3 text-[11px] uppercase tracking-[0.25em] text-carbon-900 transition-colors hover:bg-ember-300 disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="runDemo">Run the validator →</span>
                            <span wire:loading wire:target="runDemo">Validating…</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <footer class="bg-carbon-50">
        <div class="mx-auto max-w-5xl px-6 py-10 text-[10px] uppercase tracking-[0.25em] text-carbon-700">
            &copy; {{ date('Y') }} MinistrySites, LLC — MIT licensed
        </div>
    </footer>
</div>
