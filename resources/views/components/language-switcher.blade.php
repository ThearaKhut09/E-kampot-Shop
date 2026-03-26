@props([
    'mobile' => false,
])

@php
    $currentLocale = app()->getLocale();

    $locales = [
        'en' => [
            'label' => __('ui.english'),
            'short' => 'EN',
            'logo' => asset('images/languages/en.jpg'),
        ],
        'km' => [
            'label' => __('ui.khmer'),
            'short' => 'KM',
            'logo' => asset('images/languages/km.png'),
        ],
    ];

    $activeLocale = $locales[$currentLocale] ?? $locales['en'];

    $triggerSizeClass = $mobile ? 'h-8 w-8' : 'h-9 w-9';
    $menuWidthClass = $mobile ? 'w-36' : 'w-40';
@endphp

<div {{ $attributes->merge(['class' => 'relative']) }} x-data="{ open: false }">
    <span class="sr-only">{{ __('ui.language') }}</span>

    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        aria-haspopup="true"
        aria-label="{{ __('ui.language') }}"
        class="{{ $triggerSizeClass }} relative inline-flex items-center justify-center overflow-hidden rounded-full ring-2 ring-transparent transition-all duration-200 hover:ring-primary-300 focus:outline-none focus:ring-primary-500"
    >
        <img
            src="{{ $activeLocale['logo'] }}"
            alt="{{ $activeLocale['label'] }}"
            class="h-full w-full object-cover"
            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('inline-flex');"
        >
        <span class="hidden absolute inset-0 items-center justify-center rounded-full bg-gray-100 text-[10px] font-bold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
            {{ $activeLocale['short'] }}
        </span>
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        @keydown.escape.window="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute right-0 z-50 mt-2 {{ $menuWidthClass }} rounded-2xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-800"
        style="display: none;"
    >
        @foreach ($locales as $localeCode => $locale)
            <form method="POST" action="{{ route('language.switch', $localeCode) }}">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center gap-3 rounded-xl px-2.5 py-2 text-left text-sm transition-colors {{ $currentLocale === $localeCode ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700' }}"
                >
                    <span class="h-6 w-6 relative inline-flex items-center justify-center overflow-hidden rounded-full ring-1 ring-gray-200 dark:ring-gray-600">
                        <img
                            src="{{ $locale['logo'] }}"
                            alt="{{ $locale['label'] }}"
                            class="h-full w-full object-cover"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('inline-flex');"
                        >
                        <span class="hidden absolute inset-0 items-center justify-center bg-gray-100 text-[10px] font-bold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                            {{ $locale['short'] }}
                        </span>
                    </span>
                    <span>{{ $locale['label'] }}</span>
                </button>
            </form>
        @endforeach
    </div>
</div>
