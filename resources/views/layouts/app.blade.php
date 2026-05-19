@php($logoSvg = view('partials.logo')->render())
<!doctype html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#050505">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'VTR CORE' }}</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icon.svg">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VTR CORE">
    <meta name="mobile-web-app-capable" content="yes">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    @stack('styles')

    <style>
        #vtr-splash {
            position: fixed; inset: 0; z-index: 99999;
            background: #050505;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 28px;
            animation: splashOut 0.5s cubic-bezier(.4,0,.2,1) 2.1s forwards;
        }
        #vtr-splash .splash-logo {
            width: 110px; height: 110px;
            animation: splashZoom 0.7s cubic-bezier(.34,1.56,.64,1) 0.1s both,
                       splashGlow 1.2s ease-in-out 0.8s 2 alternate;
        }
        #vtr-splash .splash-label {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.55em;
            color: #ffffff;
            opacity: 0;
            animation: splashText 0.5s ease 0.85s forwards;
        }
        #vtr-splash .splash-bar {
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #e60012, transparent);
            border-radius: 99px;
            animation: splashBarGrow 1s ease 0.9s forwards;
        }
        @keyframes splashZoom {
            from { transform: scale(0.04) rotate(-8deg); opacity: 0; }
            to   { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        @keyframes splashGlow {
            from { filter: drop-shadow(0 0 6px rgba(230,0,18,0.2)); }
            to   { filter: drop-shadow(0 0 32px rgba(230,0,18,0.85)) drop-shadow(0 0 60px rgba(230,0,18,0.35)); }
        }
        @keyframes splashText {
            from { opacity: 0; letter-spacing: 0.85em; }
            to   { opacity: 0.55; letter-spacing: 0.55em; }
        }
        @keyframes splashBarGrow {
            from { width: 0; opacity: 0; }
            to   { width: 80px; opacity: 1; }
        }
        @keyframes splashOut {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0; transform: scale(1.08); pointer-events: none; }
        }
    </style>
</head>
<body class="vtr-bg vtr-grain antialiased">

    {{-- Splash Screen --}}
    <div id="vtr-splash" aria-hidden="true">
        <div class="splash-logo">
            <svg viewBox="0 0 100 100" fill="none">
                <defs>
                    <linearGradient id="splashGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#ff1a2b"/>
                        <stop offset="100%" stop-color="#8a0008"/>
                    </linearGradient>
                </defs>
                <path d="M10 18 L50 90 L90 18 L78 18 L50 64 L22 18 Z"
                      fill="url(#splashGrad)" stroke="#ff5560" stroke-width="1.5" stroke-linejoin="miter"/>
                <path d="M40 18 L50 35 L60 18 Z" fill="#ff1a2b" opacity="0.85"/>
            </svg>
        </div>
        <div class="splash-bar"></div>
        <div class="splash-label">VTR CORE</div>
    </div>
    <script>
        (function() {
            var splash = document.getElementById('vtr-splash');
            if (!splash) return;
            if (sessionStorage.getItem('vtr_launched')) {
                splash.remove();
                return;
            }
            sessionStorage.setItem('vtr_launched', '1');
            splash.addEventListener('animationend', function(e) {
                if (e.animationName === 'splashOut') splash.remove();
            });
        })();
    </script>

    <div class="relative z-10">
        {{ $slot ?? '' }}
        @yield('content')
    </div>
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }

        // PWA Install
        let _pwaPrompt = null;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            _pwaPrompt = e;
            var btn = document.getElementById('vtr-install-btn');
            if (btn) btn.style.display = 'flex';
        });
        window.addEventListener('appinstalled', function() {
            var btn = document.getElementById('vtr-install-btn');
            if (btn) btn.remove();
        });
        function vtrInstallApp() {
            if (!_pwaPrompt) return;
            _pwaPrompt.prompt();
            _pwaPrompt.userChoice.then(function() { _pwaPrompt = null; });
        }

        // Push Notifications
        const VAPID_PUBLIC = '{{ config("services.vapid.public_key") }}';

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = atob(base64);
            return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
        }

        async function vtrRequestNotifications() {
            if (!('Notification' in window) || !('serviceWorker' in navigator)) return;
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') return;

            const reg = await navigator.serviceWorker.ready;
            let sub = await reg.pushManager.getSubscription();
            if (!sub) {
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC)
                });
            }

            const key  = sub.getKey('p256dh');
            const auth = sub.getKey('auth');

            await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    endpoint: sub.endpoint,
                    p256dh:   key  ? btoa(String.fromCharCode(...new Uint8Array(key)))  : null,
                    auth:     auth ? btoa(String.fromCharCode(...new Uint8Array(auth))) : null,
                })
            });

            document.getElementById('vtr-notif-btn').style.display = 'none';
        }

        // Mostra botão se notificações ainda não foram ativadas
        if ('Notification' in window && Notification.permission !== 'granted') {
            document.addEventListener('DOMContentLoaded', function() {
                var nb = document.getElementById('vtr-notif-btn');
                if (nb) nb.style.display = 'flex';
            });
        }
    </script>

    {{-- Botão flutuante de instalar PWA --}}
    <button id="vtr-install-btn" onclick="vtrInstallApp()" title="Instalar VTR CORE"
        style="display:none; position:fixed; bottom:24px; right:24px; z-index:9999;
               align-items:center; gap:8px; padding:10px 18px;
               background:#e60012; color:#fff; border:none; border-radius:8px;
               font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.9rem;
               letter-spacing:0.08em; cursor:pointer; box-shadow:0 4px 20px rgba(230,0,18,0.4);">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
        </svg>
        INSTALAR APP
    </button>

    {{-- Botão flutuante de ativar notificações --}}
    <button id="vtr-notif-btn" onclick="vtrRequestNotifications()" title="Ativar notificações"
        style="display:none; position:fixed; bottom:80px; right:24px; z-index:9999;
               align-items:center; gap:8px; padding:10px 18px;
               background:#1a1a1a; color:#fff; border:1px solid #333; border-radius:8px;
               font-family:'Rajdhani',sans-serif; font-weight:700; font-size:0.9rem;
               letter-spacing:0.08em; cursor:pointer; box-shadow:0 4px 20px rgba(0,0,0,0.4);">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        NOTIFICAÇÕES
    </button>
</body>
</html>
