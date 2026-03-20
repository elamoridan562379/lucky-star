<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Lucky Star Coffee') }} — @yield('title', 'POS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
    </style>
</head>
<body class="h-full font-sans antialiased">
    {{ $slot }}
    @livewireScripts
    @stack('scripts')
</body>
</html>
