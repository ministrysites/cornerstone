@props([
    'number',
    'question',
    'open' => false,
])

<div x-data="{ open: @js($open) }" class="border-b border-carbon-900/30 last:border-b-0">
    <button
        type="button"
        @click="open = !open"
        class="flex w-full items-center justify-between gap-6 py-6 text-left"
        :aria-expanded="open"
    >
        <span class="flex items-baseline gap-5">
            <span class="font-mono text-xs tabular-nums text-carbon-500">
                Q.{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}
            </span>
            <span class="font-display text-xl font-bold tracking-tight text-carbon-900 md:text-2xl">
                {{ $question }}
            </span>
        </span>
        <span
            class="flex size-9 shrink-0 items-center justify-center border border-carbon-900 font-mono text-base text-carbon-900 transition-transform duration-300"
            :class="open ? 'rotate-45 bg-carbon-900 text-carbon-50' : ''"
            aria-hidden="true"
        >+</span>
    </button>

    <div x-show="open" x-cloak x-collapse>
        <p class="max-w-2xl pb-6 pl-16 text-sm leading-relaxed text-carbon-700">
            {{ $slot }}
        </p>
    </div>
</div>
