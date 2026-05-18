<aside class="hidden md:flex md:flex-col w-[120px] shrink-0 border-r border-vtr-border bg-vtr-surface/60 backdrop-blur sticky top-0 h-dvh">
    <div class="px-4 py-5 flex justify-center">
        <div class="vtr-logo w-12 h-12">@include('partials.logo')</div>
    </div>
    <nav class="flex-1 flex flex-col">
        @foreach($nav as $item)
            <a href="{{ $item['href'] ?? '#' }}" class="vtr-side-item {{ ($item['key'] ?? null) === ($active ?? null) ? 'active' : '' }}">
                @include('partials.icon', ['name' => $item['icon'], 'class' => 'w-6 h-6'])
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    @if($modoDisciplina ?? false)
        <div class="border-t border-vtr-border px-3 py-4 text-center">
            <div class="text-[9px] tracking-[0.25em] text-vtr-muted">MODO</div>
            <div class="text-[11px] tracking-[0.25em] text-white font-display">DISCIPLINA</div>
            <div class="text-vtr-red font-display text-2xl mt-1 vtr-glow">ON</div>
            <div class="vtr-slashes h-1.5 mt-2 mx-auto w-10 opacity-70"></div>
        </div>
    @endif
</aside>
