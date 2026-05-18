@extends('layouts.app', ['title' => 'VTR CORE — Login'])

@section('content')
<main class="relative min-h-dvh w-full overflow-hidden">

    {{-- Imagem de fundo (já contém rosto, listras, markers VITOR/VTR-07/FOCO/DISCIPLINA/EVOLUÇÃO/MENTE FORTE) --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/login-bg.png') }}" alt=""
             class="absolute inset-0 w-full h-full object-cover object-[30%_center] md:object-[35%_center] select-none pointer-events-none"
             draggable="false">
        {{-- escurecer o lado direito (onde fica o form) sem afetar a foto à esquerda --}}
        <div class="hidden md:block absolute inset-0"
             style="background: linear-gradient(90deg, transparent 0%, transparent 45%, rgba(0,0,0,0.55) 65%, rgba(0,0,0,0.75) 100%);"></div>
        {{-- mobile: escurecer parte de baixo onde o form aparece --}}
        <div class="md:hidden absolute inset-0"
             style="background: linear-gradient(180deg, transparent 0%, transparent 35%, rgba(0,0,0,0.55) 60%, rgba(0,0,0,0.85) 100%);"></div>
    </div>

    {{-- container do formulário --}}
    <div class="relative z-10 min-h-dvh flex items-end md:items-center justify-center md:justify-end px-4 py-6 md:px-12 md:py-10">
        <div class="vtr-card vtr-corner w-full max-w-[440px] p-7 md:p-9 flex flex-col gap-6 backdrop-blur-md"
             style="background: linear-gradient(180deg, rgba(10,10,10,0.78), rgba(5,5,5,0.85));">

            <header class="flex items-center gap-3">
                <div class="w-10 h-10">@include('partials.logo')</div>
                <div>
                    <div class="font-display text-xl tracking-[0.3em] leading-none">VTR <span class="text-vtr-red vtr-glow">CORE</span></div>
                    <div class="text-[10px] uppercase tracking-[0.3em] text-vtr-muted mt-1">Disciplina vence motivação.</div>
                </div>
            </header>

            <div>
                <div class="vtr-divider mb-3"></div>
                <h1 class="font-display text-2xl md:text-3xl">ACESSO <span class="text-vtr-red vtr-glow">RESTRITO</span></h1>
                <p class="text-xs text-vtr-muted mt-1.5 tracking-wide">Entre para continuar a sequência. Hoje é dia de não quebrar a cadeia.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf

                <label class="block">
                    <span class="text-[10px] uppercase tracking-[0.25em] text-vtr-muted">E-mail ou usuário</span>
                    <input type="email" name="email" value="{{ old('email', 'vitor@vtrcore.app') }}" required autofocus
                           class="vtr-input mt-2" placeholder="seu@email.com">
                </label>

                <label class="block">
                    <span class="text-[10px] uppercase tracking-[0.25em] text-vtr-muted">Senha</span>
                    <div class="relative">
                        <input type="password" name="password" required
                               class="vtr-input mt-2 pr-12" placeholder="••••••••" id="pwd">
                        <button type="button" onclick="const p=document.getElementById('pwd');p.type=p.type==='password'?'text':'password'"
                                class="absolute right-3 top-1/2 -translate-y-1/2 mt-1 text-vtr-muted hover:text-white">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </label>

                @if ($errors->any())
                    <div class="text-sm text-vtr-red border border-vtr-red/40 bg-vtr-red/10 rounded-lg px-3 py-2">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex items-center justify-between text-[11px] uppercase tracking-[0.18em]">
                    <label class="flex items-center gap-2 text-vtr-muted select-none">
                        <input type="checkbox" name="remember" class="accent-vtr-red w-4 h-4">
                        Lembrar de mim
                    </label>
                    <a href="#" class="text-vtr-red hover:underline">Esqueceu?</a>
                </div>

                <button type="submit" class="vtr-btn mt-1">
                    Entrar
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </form>

            <div class="flex items-center gap-3 text-[10px] uppercase tracking-[0.3em] text-vtr-muted">
                <span class="h-px flex-1 bg-vtr-border"></span>
                Disciplina · Foco · Evolução
                <span class="h-px flex-1 bg-vtr-border"></span>
            </div>

            <footer class="text-[10px] uppercase tracking-[0.25em] text-vtr-muted text-center">
                Ainda não tem uma conta?
                <a href="#" class="text-vtr-red hover:underline ml-1">Criar conta</a>
            </footer>
        </div>
    </div>
</main>
@endsection
