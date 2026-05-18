<nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-vtr-surface/95 backdrop-blur border-t border-vtr-border">
    <div class="flex">
        @foreach(array_slice($nav, 0, 5) as $item)
            <a href="{{ $item['href'] ?? '#' }}" class="vtr-bottom-item {{ ($item['key'] ?? null) === ($active ?? null) ? 'active' : '' }}">
                @include('partials.icon', ['name' => $item['icon'], 'class' => 'w-5 h-5'])
                <span>{{ \Illuminate\Support\Str::limit($item['label'], 10, '') }}</span>
            </a>
        @endforeach
    </div>
    @if($modoDisciplina ?? false)
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-vtr-red text-[10px] tracking-[0.25em] font-display px-3 py-0.5 rounded-full shadow-[0_0_12px_rgba(230,0,18,0.7)]">
            MODO DISCIPLINA ON
        </div>
    @endif
</nav>
