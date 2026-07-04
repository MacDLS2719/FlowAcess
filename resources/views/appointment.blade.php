<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ __('customer.app_name') ?? 'Agendamiento de Citas' }}</title>

    <link rel="icon" href="{{ asset('images/user.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles

    <style>
        body {
            transition: all .3s ease;
        }
    </style>
</head>

<body id="body"
    class="min-h-screen flex items-center justify-center bg-gray-100 text-gray-900">

    {{-- ================= TOP BAR ================= --}}
    <div class="fixed top-4 right-4 flex gap-2 z-50">

        {{-- LANG ES --}}
        <a href="{{ route('lang.switch','es') }}"
           class="px-3 py-2 rounded-lg text-white bg-amber-500 hover:bg-amber-600 text-sm">
            🇪🇸 ES
        </a>

        {{-- LANG EN --}}
        <a href="{{ route('lang.switch','en') }}"
           class="px-3 py-2 rounded-lg text-white bg-gray-700 hover:bg-gray-800 text-sm">
            🇺🇸 EN
        </a>

        {{-- THEME BUTTON --}}
        <button id="themeButton"
                onclick="toggleTheme()"
                class="px-3 py-2 rounded-lg bg-gray-900 text-white text-sm">
            🌙
        </button>

    </div>

    {{-- ================= MAIN CONTAINER ================= --}}
    <div class="w-full max-w-4xl p-4">

        <div class="text-center mb-6">

            <h1 id="title" class="text-2xl font-bold">
                {{ __('customer.app_name') ?? 'Agendamiento de Citas' }}
            </h1>

            <p id="subtitle" class="text-gray-500">
                {{ __('customer.subtitle') ?? 'Selecciona tu horario disponible' }}
            </p>

        </div>

        {{-- LIVEWIRE CONTENT --}}
        {{ $slot }}

    </div>

    {{-- ================= THEME SCRIPT ================= --}}
    <script>

        const body = document.getElementById('body');
        const button = document.getElementById('themeButton');
        const title = document.getElementById('title');
        const subtitle = document.getElementById('subtitle');

        function applyTheme(theme) {

            if (theme === 'dark') {

                body.classList.remove('bg-gray-100', 'text-gray-900');
                body.classList.add('bg-gray-900', 'text-white');

                title.classList.add('text-white');
                subtitle.classList.remove('text-gray-500');
                subtitle.classList.add('text-gray-300');

                button.innerHTML = '☀️';
                button.classList.remove('bg-gray-900');
                button.classList.add('bg-yellow-500', 'text-black');

                body.setAttribute('data-theme', 'dark');

            } else {

                body.classList.add('bg-gray-100', 'text-gray-900');
                body.classList.remove('bg-gray-900', 'text-white');

                title.classList.remove('text-white');
                subtitle.classList.remove('text-gray-300');
                subtitle.classList.add('text-gray-500');

                button.innerHTML = '🌙';
                button.classList.add('bg-gray-900', 'text-white');
                button.classList.remove('bg-yellow-500', 'text-black');

                body.setAttribute('data-theme', 'light');
            }
        }

        const savedTheme = localStorage.getItem('theme') ?? 'light';
        applyTheme(savedTheme);

        function toggleTheme() {

            const current = body.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';

            localStorage.setItem('theme', next);
            applyTheme(next);
        }

    </script>

    @livewireScripts
</body>

</html>