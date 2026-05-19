@extends('layouts.shell', [
    'active'         => 'treinos',
    'title'          => 'VTR CORE — Treinos',
    'modoDisciplina' => $profile->modo_disciplina_on ?? false,
])

@section('shell-content')

    {{-- HERO --}}
    @include('partials.hero', [
        'userName'   => $user->name,
        'heroFrase'  => 'Corpo em ação. Mente em evolução.',
        'heroFoco'   => 'TREINOS',
        'heroStatus' => $statusHoje['concluido'] ? 'CONCLUÍDO' : ($treinoHoje['workout'] ? ($statusHoje['passado'] ? 'HISTÓRICO' : 'ATIVO') : 'DESCANSO'),
        'vtrNumber'  => $profile->vtr_number ?? '07',
        'heroPhoto'  => true,
    ])

    <section class="relative z-10 px-4 md:px-10 mt-6 space-y-5">

        @if(session('ok'))
            <div class="vtr-card border-vtr-red/40 bg-vtr-red/5 px-4 py-2 text-[12px] tracking-[0.18em]">{{ session('ok') }}</div>
        @endif

        {{-- Banner navegação quando não está vendo o dia de hoje --}}
        @if(! $treinoHoje['eh_hoje'])
            <div class="vtr-card vtr-corner px-4 py-3 flex items-center gap-3 flex-wrap">
                <span class="text-vtr-red">@include('partials.icon', ['name' => 'calendar', 'class' => 'w-4 h-4'])</span>
                <div class="text-[12px] tracking-[0.18em] flex-1">
                    VOCÊ ESTÁ VENDO <span class="text-vtr-red">{{ $treinoHoje['dia_label'] }} · {{ $treinoHoje['data_br'] }}</span>
                    {{ $treinoHoje['eh_passado'] ? '(somente leitura)' : '(programado)' }}
                </div>
                <a href="{{ route('treinos') }}" class="text-[10px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-1.5 hover:border-vtr-red/60">VOLTAR PARA HOJE</a>
            </div>
        @endif

        {{-- ========== Linha 1: Status do dia | Próximo treino ========== --}}
        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-5">

            {{-- STATUS DO DIA --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 relative {{ $statusHoje['concluido'] ? 'border-vtr-red/70' : '' }}">
                <div class="flex items-start gap-5">
                    <div class="vtr-ring shrink-0" style="--val: {{ $statusHoje['percent'] }}; width:120px; height:120px;">
                        <div>
                            @if($statusHoje['concluido'])
                                <span class="text-vtr-red">
                                    @include('partials.icon', ['name' => 'checklist', 'class' => 'w-8 h-8 mx-auto'])
                                </span>
                            @else
                                <div class="font-display text-2xl text-white leading-none">{{ $statusHoje['percent'] }}%</div>
                                <div class="text-[9px] tracking-[0.18em] text-vtr-muted mt-1">PROGRESSO</div>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-[10px] tracking-[0.22em] text-vtr-muted">{{ $treinoHoje['dia_label'] }} · STATUS</div>
                        <h2 class="font-display tracking-[0.22em] text-2xl md:text-3xl text-vtr-red mt-1 leading-none">
                            {{ $statusHoje['titulo'] }}
                        </h2>
                        <div class="text-[12px] tracking-[0.18em] text-white/85 mt-2 uppercase">{{ $statusHoje['subtitulo'] }}</div>
                        <p class="text-sm text-vtr-text/85 mt-2">{{ $statusHoje['detalhe'] }}</p>

                        @if(! $statusHoje['concluido'] && $treinoHoje['workout'])
                            <div class="vtr-progress mt-4"><span style="width: {{ $statusHoje['percent'] }}%"></span></div>
                        @endif
                    </div>
                </div>

                <blockquote class="mt-4 text-xs text-vtr-muted italic relative pl-3 border-l-2 border-vtr-red/60">
                    “ Constância constrói o físico. ”
                </blockquote>
            </div>

            {{-- PRÓXIMO TREINO --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 flex flex-col">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> PRÓXIMO TREINO
                </h2>
                <div class="flex flex-col items-center text-center flex-1 justify-center">
                    <div class="w-12 h-12 rounded-full border-2 border-vtr-red/70 grid place-items-center mb-3">
                        @include('partials.icon', ['name' => $amanha['icone'], 'class' => 'w-6 h-6 text-vtr-red'])
                    </div>
                    <div class="font-display text-vtr-red text-xs tracking-[0.25em]">{{ $amanha['eh_amanha'] ? 'AMANHÃ' : $amanha['curto'] }}</div>
                    <div class="font-display text-2xl mt-1">{{ mb_strtoupper($amanha['titulo']) }}</div>
                    <div class="text-[10px] tracking-[0.18em] text-vtr-muted mt-2">{{ $amanha['intensidade'] }}</div>
                    <div class="text-[11px] tracking-[0.22em] text-white/80 mt-3">{{ $amanha['total_exercicios'] }} EXERCÍCIOS</div>
                </div>
            </div>
        </div>

        {{-- ========== Linha 2: Split semanal (clicável) ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> SPLIT SEMANAL
            </h2>
            <div class="grid grid-cols-7 gap-2">
                @foreach($splitSemanal as $d)
                    @php
                        $base = 'vtr-card p-2 md:p-3 flex flex-col items-center text-center transition-colors';
                        if ($d['selecionado']) $base .= ' border-vtr-red ring-2 ring-vtr-red/50';
                        elseif ($d['ativo']) $base .= ' border-vtr-red/70';
                        if ($d['passou'] && !$d['feito']) $base .= ' opacity-50 grayscale';
                        elseif ($d['descanso']) $base .= ' opacity-60';
                    @endphp
                    <a href="{{ route('treinos', ['dia' => $d['dia_iso']]) }}" class="{{ $base }} hover:border-vtr-red/70 cursor-pointer">
                        <div class="font-display tracking-[0.18em] text-[10px] {{ $d['selecionado'] || $d['ativo'] ? 'text-vtr-red' : 'text-vtr-muted' }}">{{ $d['dia'] }}</div>
                        <span class="my-2 {{ $d['passou'] && !$d['feito'] ? 'text-vtr-muted' : 'text-vtr-red' }}">
                            @include('partials.icon', ['name' => $d['icone'], 'class' => 'w-5 h-5 md:w-6 md:h-6'])
                        </span>
                        <div class="text-[10px] leading-tight min-h-[28px]">{{ $d['titulo'] }}</div>
                        @if($d['feito'])
                            <span class="vtr-check mt-2 w-5 h-5">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        @elseif($d['passou'] && !$d['descanso'] && $d['percent'] > 0)
                            <span class="text-[9px] mt-2 tracking-[0.18em] text-vtr-muted">{{ $d['percent'] }}%</span>
                        @else
                            <span class="vtr-check empty mt-2 w-5 h-5"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ========== Linha 3: EXERCÍCIOS DO DIA ========== --}}
        @if($treinoHoje['workout'] && $exercicios->count())
            <div class="vtr-card vtr-corner p-5 md:p-6 {{ $treinoHoje['eh_passado'] ? 'opacity-90' : '' }}">
                <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
                    <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3">
                        <span class="vtr-divider"></span> EXERCÍCIOS · {{ mb_strtoupper($treinoHoje['titulo']) }}
                    </h2>
                    <div class="flex items-center gap-3 text-[11px] tracking-[0.22em] text-vtr-muted">
                        <span>{{ $treinoHoje['series_feitas'] }}/{{ $treinoHoje['series_total'] }} SÉRIES</span>
                        <a href="{{ route('workouts.edit', $treinoHoje['workout']) }}" class="font-display border border-vtr-border rounded px-3 py-1.5 hover:border-vtr-red/60">EDITAR</a>
                        <a href="{{ route('workouts.manage') }}" class="font-display border border-vtr-border rounded px-3 py-1.5 hover:border-vtr-red/60">GERENCIAR</a>
                    </div>
                </div>

                <ul class="space-y-3">
                    @foreach($exercicios as $ex)
                        <li x-data="exData(@json($ex))"
                            :class="concluido ? 'vtr-card p-4 border-vtr-red/40 bg-vtr-red/5' : 'vtr-card p-4'">
                            <div class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-full border border-vtr-border grid place-items-center text-vtr-red shrink-0">
                                    @include('partials.icon', ['name' => $ex['icone'], 'class' => 'w-5 h-5'])
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-display tracking-[0.05em] text-base flex-1 min-w-0">{{ $ex['nome'] }}</h3>
                                        <span class="font-display text-vtr-red text-sm shrink-0">{{ $ex['rotulo'] }}</span>
                                    </div>
                                    @if($ex['observacao'])
                                        <div class="text-[11px] text-vtr-muted mt-1">{{ $ex['observacao'] }}</div>
                                    @endif

                                    {{-- Bubbles de série --}}
                                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                                        @if($treinoHoje['eh_hoje'])
                                            <template x-for="s in series" :key="s.n">
                                                <button type="button"
                                                    @click="toggle(s.n)"
                                                    :disabled="loading"
                                                    :class="s.feita
                                                        ? 'w-10 h-10 md:w-11 md:h-11 rounded-full border-2 grid place-items-center font-display text-sm transition-colors border-vtr-red bg-vtr-red text-white'
                                                        : 'w-10 h-10 md:w-11 md:h-11 rounded-full border-2 grid place-items-center font-display text-sm transition-colors border-vtr-border text-vtr-muted hover:border-vtr-red/60'">
                                                    <template x-if="s.feita">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </template>
                                                    <template x-if="!s.feita">
                                                        <span x-text="s.n"></span>
                                                    </template>
                                                </button>
                                            </template>

                                            <button type="button" x-show="!concluido && series.length > 1"
                                                @click="concluirTodas()"
                                                class="ml-auto text-[10px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60 transition-colors">
                                                CONCLUIR TODAS
                                            </button>
                                        @else
                                            @foreach($ex['series'] as $s)
                                                <span class="w-10 h-10 md:w-11 md:h-11 rounded-full border-2 grid place-items-center font-display text-sm cursor-not-allowed
                                                    {{ $s['feita']
                                                        ? 'border-vtr-red/60 bg-vtr-red/40 text-white/90'
                                                        : 'border-vtr-border/50 text-vtr-muted/60' }}"
                                                    title="Série {{ $s['n'] }}{{ $s['feita'] ? ' (feita)' : '' }}">
                                                    @if($s['feita'])
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    @else
                                                        {{ $s['n'] }}
                                                    @endif
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>

                                    {{-- Última carga registrada (se houver) --}}
                                    @php
                                        $ultima = collect($ex['series'])->last(fn($s) => !is_null($s['carga']) || !is_null($s['reps']));
                                    @endphp
                                    @if($ultima)
                                        <div class="mt-2 text-[11px] text-vtr-muted">
                                            {{ $treinoHoje['eh_passado'] ? 'Naquele dia:' : 'Último:' }}
                                            @if($ultima['carga']) <span class="text-white">{{ rtrim(rtrim(number_format($ultima['carga'], 2, ',', '.'), '0'), ',') }} kg</span> @endif
                                            @if($ultima['reps']) <span class="text-white">· {{ $ultima['reps'] }} reps</span> @endif
                                            @if($ultima['segundos']) <span class="text-white">· {{ $ultima['segundos'] }}s</span> @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            {{-- DIA SEM TREINO --}}
            <div class="vtr-card vtr-corner p-8 md:p-10 text-center">
                <span class="inline-block text-vtr-red mb-3">
                    @include('partials.icon', ['name' => 'leaf', 'class' => 'w-10 h-10 mx-auto'])
                </span>
                <h2 class="font-display tracking-[0.22em] text-xl md:text-2xl">DIA DE DESCANSO</h2>
                <p class="text-sm text-vtr-muted mt-2">{{ $treinoHoje['eh_hoje'] ? 'Recuperação é parte do treino. Volte amanhã.' : 'Sem treino programado para este dia.' }}</p>
                @if($treinoHoje['eh_hoje'])
                    <p class="text-[11px] tracking-[0.22em] text-vtr-red mt-4">PRÓXIMO: {{ $amanha['curto'] }} · {{ mb_strtoupper($amanha['titulo']) }}</p>
                @endif
                <div class="mt-5">
                    <a href="{{ route('workouts.manage') }}" class="text-[10px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60">GERENCIAR TREINOS</a>
                </div>
            </div>
        @endif

        {{-- ========== Linha 4: Consistência ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> CONSISTÊNCIA · 4 SEMANAS
            </h2>
            <div class="flex items-center gap-5 flex-wrap">
                <div class="vtr-ring shrink-0" style="--val: {{ $consistencia['percent'] }}; width:120px; height:120px;">
                    <div>
                        <div class="font-display text-2xl text-white">{{ $consistencia['percent'] }}%</div>
                        <div class="text-[9px] tracking-[0.18em] text-vtr-muted mt-0.5 leading-tight">{{ $consistencia['concluidos'] }} DIAS<br>TREINADOS</div>
                    </div>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <div class="grid grid-cols-4 gap-3 text-center">
                        @foreach($consistencia['serie'] as $p)
                            <div>
                                <div class="font-display text-2xl text-vtr-red">{{ $p['y'] }}</div>
                                <div class="text-[10px] tracking-[0.18em] text-vtr-muted mt-1">SEM. {{ $p['x'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== Frase rodapé ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6 flex items-center gap-4">
            <div class="text-vtr-red text-3xl font-display leading-none">“</div>
            <p class="flex-1 text-sm md:text-base text-vtr-text/90 italic">
                Disciplina no treino é repetir o certo até virar resultado.
            </p>
            <div class="font-display tracking-[0.25em] text-xs text-vtr-muted hidden md:block">VTR <span class="text-vtr-red">TRAIN</span></div>
        </div>
    </section>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
const _csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';

function exData(ex) {
    return {
        series:  ex.series.map(s => ({ n: s.n, feita: s.feita })),
        loading: false,

        get concluido() {
            return this.series.length > 0 && this.series.every(s => s.feita);
        },

        async toggle(n) {
            if (this.loading) return;
            const s = this.series.find(s => s.n === n);
            if (!s) return;

            // Optimistic update
            s.feita = !s.feita;
            this.loading = true;

            try {
                await fetch(`/treinos/exercicios/${ex.id}/series/${n}/toggle`, {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
                });
            } catch {
                s.feita = !s.feita; // Revert on error
            } finally {
                this.loading = false;
            }
        },

        async concluirTodas() {
            if (this.loading) return;
            this.series.forEach(s => s.feita = true); // Optimistic
            this.loading = true;

            try {
                await fetch(`/treinos/exercicios/${ex.id}/concluir`, {
                    method:  'POST',
                    headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json' },
                });
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
