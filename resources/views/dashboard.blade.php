@extends('layouts.shell', [
    'active'         => 'inicio',
    'title'          => 'VTR CORE — Dashboard',
    'modoDisciplina' => $profile->modo_disciplina_on ?? false,
])

@php
    use Illuminate\Support\Carbon;
    $hoje = Carbon::now();
@endphp

@section('shell-content')

        {{-- HERO --}}
        @include('partials.hero', [
            'userName'   => $user->name,
            'heroFrase'  => $profile->frase_principal,
            'heroFoco'   => $profile->foco,
            'heroStatus' => $profile->status,
            'vtrNumber'  => $profile->vtr_number ?? '07',
            'heroPhoto'  => true,
        ])

        {{-- ========== Resumo do Dia ========== --}}
        <section class="relative z-10 px-4 md:px-10 mt-6 space-y-5">

            <div class="vtr-card vtr-corner p-5 md:p-6">
                <header class="flex items-center justify-between mb-5">
                    <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3">
                        <span class="vtr-divider"></span> RESUMO DO DIA
                    </h2>
                    <span class="text-xs text-vtr-muted">{{ $hoje->translatedFormat('d \d\e F, Y') }}</span>
                </header>

                <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr_1fr] gap-4">
                    {{-- Objetivo principal --}}
                    <div class="vtr-card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            @include('partials.icon', ['name' => 'target', 'class' => 'w-4 h-4 text-vtr-red'])
                            <span class="text-[11px] tracking-[0.22em] text-vtr-red font-display">OBJETIVO PRINCIPAL</span>
                        </div>
                        <h3 class="font-display text-2xl md:text-3xl">{{ $main?->titulo ?? '—' }}</h3>
                        @if($main?->prazo)
                            <p class="text-[11px] tracking-[0.22em] text-vtr-muted mt-1 uppercase">Meta até {{ $main->prazo->format('d/m/Y') }}</p>
                        @endif
                        <div class="mt-4 flex items-center gap-3">
                            <div class="vtr-progress flex-1"><span style="width: {{ $main?->progresso ?? 0 }}%"></span></div>
                            <div class="font-display text-vtr-red text-2xl">{{ $main?->progresso ?? 0 }}%</div>
                        </div>
                        @if($main?->frase)
                            <blockquote class="mt-4 text-xs text-vtr-muted italic relative pl-3 border-l-2 border-vtr-red/60">
                                {{ $main->frase }}
                            </blockquote>
                        @endif
                    </div>

                    {{-- Foco de hoje --}}
                    <div class="vtr-card p-5 flex flex-col">
                        <div class="flex items-center gap-2 mb-3">
                            @include('partials.icon', ['name' => 'book', 'class' => 'w-4 h-4 text-vtr-red'])
                            <span class="text-[11px] tracking-[0.22em] text-vtr-red font-display">FOCO DE HOJE</span>
                        </div>
                        <h3 class="font-display text-2xl">{{ $foco?->titulo ?? '—' }}</h3>
                        <p class="text-xs text-vtr-muted mt-1 uppercase tracking-[0.18em]">{{ $foco?->subtitulo }}</p>

                        <div class="flex items-center gap-2 mt-4">
                            @for($i = 1; $i <= ($foco->total_marcadores ?? 0); $i++)
                                <span class="vtr-check {{ $i > ($foco->marcadores_concluidos ?? 0) ? 'empty' : '' }}">
                                    @if($i <= ($foco->marcadores_concluidos ?? 0))
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @endif
                                </span>
                            @endfor
                        </div>
                    </div>

                    {{-- Dias seguidos --}}
                    <div class="vtr-card p-5 flex flex-col">
                        <div class="flex items-center gap-2 mb-3">
                            @include('partials.icon', ['name' => 'flame', 'class' => 'w-4 h-4 text-vtr-red'])
                            <span class="text-[11px] tracking-[0.22em] text-vtr-red font-display">DIAS SEGUIDOS</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <div class="font-display text-vtr-red text-6xl leading-none vtr-glow">{{ $streak->dias }}</div>
                            <div class="font-display tracking-[0.25em] text-vtr-muted pb-1">DIAS</div>
                        </div>
                        {{-- mini-line chart fake --}}
                        <svg viewBox="0 0 100 30" class="w-full mt-3" preserveAspectRatio="none">
                            <path d="M0 25 L15 22 L25 24 L40 18 L55 14 L70 16 L82 8 L100 4"
                                  stroke="#e60012" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            <path d="M0 25 L15 22 L25 24 L40 18 L55 14 L70 16 L82 8 L100 4 L100 30 L0 30 Z"
                                  fill="url(#g1)" opacity="0.4"/>
                            <defs>
                                <linearGradient id="g1" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#e60012"/>
                                    <stop offset="100%" stop-color="transparent"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ========== Ações rápidas ========== --}}
            <div class="vtr-card vtr-corner p-5 md:p-6">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> AÇÕES RÁPIDAS
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach([
                        ['target','Novo Objetivo'],
                        ['dumbbell','Registrar Treino'],
                        ['cutlery','Adicionar Refeição'],
                        ['note','Nova Anotação'],
                    ] as [$icon,$label])
                        <button class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                            @include('partials.icon', ['name' => $icon, 'class' => 'w-5 h-5 text-vtr-red'])
                            <span class="font-display tracking-[0.18em] text-[11px] uppercase">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ========== Visão geral ========== --}}
            <div class="vtr-card vtr-corner p-5 md:p-6">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> VISÃO GERAL
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Alimentação --}}
                    <div class="vtr-card p-5 flex flex-col items-center text-center">
                        <div class="w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display">
                            <span class="text-vtr-red flex items-center gap-1">
                                @include('partials.icon', ['name' => 'cutlery', 'class' => 'w-4 h-4'])
                                ALIMENTAÇÃO
                            </span>
                            <span class="text-vtr-muted">{{ $meal->concluido ? 'CONCLUÍDO' : 'HOJE' }}</span>
                        </div>
                        <div class="vtr-ring my-5" style="--val: {{ $meal->percent }}">
                            <div>
                                @if($meal->concluido)
                                    <span class="text-vtr-red">
                                        @include('partials.icon', ['name' => 'checklist', 'class' => 'w-8 h-8 mx-auto'])
                                    </span>
                                @else
                                    <div class="font-display text-3xl text-white">{{ $meal->feitas }}</div>
                                    <div class="text-xs text-vtr-muted">/ {{ $meal->total }}</div>
                                    <div class="text-[10px] tracking-[0.25em] text-vtr-red mt-1">FEITOS</div>
                                @endif
                            </div>
                        </div>
                        @if($meal->concluido)
                            <div class="text-[10px] tracking-[0.25em] text-vtr-muted">DIA CONCLUÍDO</div>
                            <div class="font-display text-vtr-red text-base mt-1">PREPARE-SE PARA AMANHÃ</div>
                        @else
                            <div class="text-[10px] tracking-[0.25em] text-vtr-muted">PRÓXIMA REFEIÇÃO</div>
                            <div class="font-display text-2xl text-vtr-red">
                                {{ $meal->proxima['horario'] }}
                                <span class="text-xs">{{ $meal->proxima['titulo'] }}</span>
                            </div>
                        @endif
                        <a href="{{ route('alimentacao') }}" class="mt-4 w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display border-t border-vtr-border pt-3">
                            <span>VER PLANO ALIMENTAR</span>
                            <span>→</span>
                        </a>
                    </div>

                    {{-- Treino --}}
                    <div class="vtr-card p-5 flex flex-col items-center text-center">
                        <div class="w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display">
                            <span class="text-vtr-red flex items-center gap-1">
                                @include('partials.icon', ['name' => 'dumbbell', 'class' => 'w-4 h-4'])
                                TREINO
                            </span>
                            <span class="text-vtr-muted">HOJE</span>
                        </div>

                        <div class="vtr-ring my-4" style="--val: {{ $workout->percent }}; width:120px; height:120px;">
                            <div>
                                @if($workout->concluido)
                                    <span class="text-vtr-red">
                                        @include('partials.icon', ['name' => 'checklist', 'class' => 'w-7 h-7 mx-auto'])
                                    </span>
                                @elseif($workout->tem_treino)
                                    <div class="font-display text-2xl text-white leading-none">{{ $workout->feitas }}<span class="text-sm text-vtr-muted">/{{ $workout->total }}</span></div>
                                    <div class="text-[9px] tracking-[0.18em] text-vtr-muted mt-1">SÉRIES</div>
                                @else
                                    <span class="text-vtr-red">
                                        @include('partials.icon', ['name' => 'leaf', 'class' => 'w-7 h-7 mx-auto'])
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="font-display text-2xl">{{ mb_strtoupper($workout->titulo) }}</div>

                        @if($workout->concluido)
                            <div class="text-[10px] tracking-[0.25em] text-vtr-red mt-2">DIA CONCLUÍDO</div>
                            <div class="text-[10px] tracking-[0.18em] text-vtr-muted mt-1">PRÓXIMO: {{ $workout->proximo['curto'] }} · {{ mb_strtoupper($workout->proximo['titulo']) }}</div>
                        @elseif($workout->tem_treino)
                            <div class="text-[10px] tracking-[0.25em] text-vtr-muted mt-2">EM ANDAMENTO</div>
                            <div class="vtr-progress w-full mt-2"><span style="width: {{ $workout->percent }}%"></span></div>
                        @else
                            <div class="text-[10px] tracking-[0.25em] text-vtr-muted mt-2">PRÓXIMO TREINO</div>
                            <div class="font-display text-vtr-red mt-0.5 text-sm">{{ $workout->proximo['curto'] }} · {{ mb_strtoupper($workout->proximo['titulo']) }}</div>
                        @endif

                        <a href="{{ route('treinos') }}" class="mt-4 w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display border-t border-vtr-border pt-3">
                            <span>VER TREINO</span>
                            <span>→</span>
                        </a>
                    </div>

                    {{-- Anotações --}}
                    <div class="vtr-card p-5 flex flex-col">
                        <div class="w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display">
                            <span class="text-vtr-red flex items-center gap-1">
                                @include('partials.icon', ['name' => 'note', 'class' => 'w-4 h-4'])
                                ANOTAÇÕES
                            </span>
                            <span class="text-vtr-muted">RECENTES</span>
                        </div>
                        <ul class="mt-5 space-y-4 flex-1">
                            @forelse($notes as $note)
                                <li class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-md border border-vtr-border-strong grid place-items-center">
                                        @include('partials.icon', ['name' => 'note', 'class' => 'w-4 h-4 text-vtr-red'])
                                    </span>
                                    <div class="flex-1">
                                        <div class="text-sm">{{ $note->titulo }}</div>
                                        <div class="text-[10px] tracking-[0.18em] text-vtr-muted">{{ $note->data->format('d/m/Y') }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-vtr-muted">Sem anotações ainda.</li>
                            @endforelse
                        </ul>
                        <button class="mt-4 w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display border-t border-vtr-border pt-3">
                            <span>VER MEUS BOOKS</span>
                            <span>→</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ========== Frase ========== --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 flex items-center gap-4">
                <div class="text-vtr-red text-3xl font-display">“</div>
                <p class="flex-1 text-sm md:text-base text-vtr-text/90 italic">
                    Disciplina é fazer o que precisa ser feito, mesmo quando você não quer fazer.
                </p>
                <div class="font-display tracking-[0.25em] text-xs text-vtr-muted hidden md:block">VTR <span class="text-vtr-red">CORE</span></div>
            </div>

        </section>
@endsection
