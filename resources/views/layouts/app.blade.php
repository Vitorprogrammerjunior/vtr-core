@php($logoSvg = view('partials.logo')->render())
<!doctype html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#050505">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'VTR CORE' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('styles')
</head>
<body class="vtr-bg vtr-grain antialiased">
    <div class="relative z-10">
        {{ $slot ?? '' }}
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
