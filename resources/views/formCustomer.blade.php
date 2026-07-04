<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

    <head>
        <meta charset="UTF-8">
        <title>{{ __('customer.app_name') }}</title>
        <link rel="icon" href="{{ asset('images/user.png') }}">
        @livewireStyles
    </head>

    <body id="body"
    style="
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f4f4;
    transition:.3s;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    ">

    <div
    style="
    position:absolute;
    top:20px;
    right:20px;
    display:flex;
    gap:10px;
    ">

    <a href="{{ route('lang.switch','es') }}"
    style="
    text-decoration:none;
    padding:8px 15px;
    background:#F59E0B;
    color:white;
    border-radius:8px;
    ">
    🇪🇸 ES
    </a>

    <a href="{{ route('lang.switch','en') }}"
    style="
    text-decoration:none;
    padding:8px 15px;
    background:#374151;
    color:white;
    border-radius:8px;
    ">
    🇺🇸 EN
    </a>

    <button
    id="themeButton"
    onclick="toggleTheme()"
    style="
    padding:8px 15px;
    border:none;
    cursor:pointer;
    border-radius:8px;
    background:#111827;
    color:white;
    ">

    🌙

    </button>

    </div>

    <div
    style="
    width:100%;
    max-width:450px;
    text-align:center;
    ">

    <h1 id="title">
        {{ __('customer.app_name') }}
    </h1>

    <p id="subtitle" style="color:#6B7280;">
        {{ __('customer.subtitle') }}
    </p>

    <livewire:customer-form />

    </div>

    <script>

    const body = document.getElementById('body');
    const button = document.getElementById('themeButton');
    const title = document.getElementById('title');
    const subtitle = document.getElementById('subtitle');

    function applyTheme(theme){

        if(theme === 'dark'){

            body.style.background='#111827';
            body.style.color='white';

            title.style.color='white';
            subtitle.style.color='#D1D5DB';

            button.innerHTML='☀️';

            body.setAttribute('data-theme','dark');

        }else{

            body.style.background='#f4f4f4';
            body.style.color='black';

            title.style.color='black';
            subtitle.style.color='#6B7280';

            button.innerHTML='🌙';

            body.setAttribute('data-theme','light');

        }

    }

    const savedTheme = localStorage.getItem('theme') ?? 'light';

    applyTheme(savedTheme);

    function toggleTheme(){

        const current = body.getAttribute('data-theme');

        const next = current === 'dark'
            ? 'light'
            : 'dark';

        localStorage.setItem('theme', next);

        applyTheme(next);

    }

    </script>


    @livewireScripts

    </body>

</html>