<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'SIBALOG POS') }}</title>

    {{-- GOOGLE FONTS: PLUS JAKARTA SANS & JETBRAINS MONO --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,700&display=swap" rel="stylesheet">

    {{-- CLOUDFLARE TURNSTILE CAPTCHA --}}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    {{-- SCRIPTS & STYLES (VUE 3 + INERTIA) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="font-sans antialiased h-full text-slate-900 bg-slate-50 selection:bg-[#00AA13] selection:text-white">
    @inertia
</body>
</html>
