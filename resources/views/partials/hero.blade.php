{{--
    Hero reutilizável.
    Vars esperadas:
      - $userName        (string)
      - $heroFrase       (string)
      - $heroFoco        (string)
      - $heroStatus      (string)
      - $vtrNumber       (string|int)
      - $heroPhoto       (bool, default true)  — se exibe a foto absoluta
--}}
<section class="relative overflow-visible">
    <div class="relative px-4 pt-5 md:pt-10 md:px-10 md:pr-0 grid md:grid-cols-[1fr_minmax(280px,520px)] items-end gap-6 md:gap-10 md:min-h-[420px]">
        <div class="relative z-10">
            <p class="text-[11px] uppercase tracking-[0.3em] text-vtr-muted">Bem-vindo de volta,</p>
            <h1 class="font-display text-[58px] sm:text-[78px] md:text-[110px] leading-[0.85] text-vtr-red vtr-glow tracking-[0.06em]">
                {{ mb_strtoupper($userName) }}
            </h1>
            <p class="mt-2 text-sm md:text-base text-vtr-muted uppercase tracking-[0.18em]">{{ $heroFrase }}</p>

            <div class="mt-4 flex items-center gap-5 text-[11px] tracking-[0.22em] uppercase">
                <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-vtr-red"></span> Foco: <strong class="text-white">{{ $heroFoco }}</strong></div>
                <div class="text-vtr-muted">|</div>
                <div class="flex items-center gap-2 text-vtr-muted">Status: <strong class="text-white">{{ $heroStatus }}</strong></div>
            </div>
        </div>

        {{-- coluna direita: reserva pra markers + topbar --}}
        <div class="relative md:self-stretch h-[280px] md:h-full">
            <div class="hidden md:block absolute top-5 right-6 z-40 text-right font-display tracking-[0.25em] text-[10px] text-white/80">
                <div class="text-vtr-red">||||</div>
                <div class="mt-1">VTR</div>
                <div class="text-2xl text-white leading-none">{{ $vtrNumber }}</div>
                <div class="mt-3 opacity-60">⊕</div>
                <div class="opacity-30 text-2xl mt-2">+ + +</div>
            </div>

            <div class="hidden md:flex absolute top-5 right-24 gap-3 z-40">
                <button class="relative w-10 h-10 rounded-full border border-vtr-border bg-black/40 flex items-center justify-center">
                    @include('partials.icon', ['name' => 'bell', 'class' => 'w-5 h-5'])
                    <span class="absolute -top-0.5 -right-0.5 bg-vtr-red text-[10px] rounded-full w-4 h-4 grid place-items-center">3</span>
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-10 h-10 rounded-full border border-vtr-border bg-black/40 flex items-center justify-center" title="Sair">
                        @include('partials.icon', ['name' => 'user', 'class' => 'w-5 h-5'])
                    </button>
                </form>
            </div>
        </div>

        @if($heroPhoto ?? true)
            <img src="{{ asset('images/foto-dashboard.png') }}" alt=""
                 class="absolute z-20 select-none pointer-events-none
                        right-0 bottom-[-16px] h-[260px]
                        md:right-[5%] md:bottom-[-24px] md:h-[400px]
                        object-contain object-bottom"
                 draggable="false">
        @endif
    </div>
</section>
