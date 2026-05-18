@extends('layouts.shell', [
    'active' => 'treinos',
    'title'  => 'VTR CORE — Gerenciar Treinos',
])

@section('shell-content')

    <section class="relative z-10 px-4 md:px-10 mt-8 space-y-5">

        <div class="flex items-center justify-between flex-wrap gap-3">
            <h1 class="font-display tracking-[0.25em] text-2xl md:text-3xl flex items-center gap-3">
                <span class="vtr-divider"></span> GERENCIAR TREINOS
            </h1>
            <a href="{{ route('treinos') }}" class="text-[11px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60">VOLTAR</a>
        </div>

        @if(session('ok'))
            <div class="vtr-card border-vtr-red/40 bg-vtr-red/5 px-4 py-2 text-[12px] tracking-[0.18em]">{{ session('ok') }}</div>
        @endif
        @if($errors->any())
            <div class="vtr-card border-vtr-red/40 bg-vtr-red/5 px-4 py-2 text-[12px] text-vtr-red">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        {{-- LISTA DE TREINOS --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> SEUS TREINOS
            </h2>

            @if($workouts->isEmpty())
                <p class="text-sm text-vtr-muted">Nenhum treino cadastrado. Crie um abaixo.</p>
            @else
                <ul class="space-y-3">
                    @foreach($workouts as $w)
                        <li class="vtr-card p-4 flex items-center gap-4 flex-wrap">
                            <span class="w-10 h-10 rounded-full border border-vtr-border grid place-items-center text-vtr-red shrink-0">
                                @include('partials.icon', ['name' => $w->icone ?: 'dumbbell', 'class' => 'w-5 h-5'])
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="font-display text-base">{{ $w->nome }}</div>
                                <div class="text-[11px] tracking-[0.18em] text-vtr-muted">
                                    {{ $w->dia_semana ? ($diasLabel[$w->dia_semana] ?? '') : 'SEM DIA' }}
                                    @if($w->grupo_muscular) · {{ $w->grupo_muscular }} @endif
                                    @if($w->intensidade) · {{ strtoupper($w->intensidade) }} @endif
                                    · {{ $w->exercises->count() }} EXERCÍCIOS
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('workouts.edit', $w) }}" class="text-[10px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60">EDITAR</a>
                                <form method="POST" action="{{ route('workouts.destroy', $w) }}" onsubmit="return confirm('Apagar treino {{ $w->nome }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60 text-vtr-muted hover:text-vtr-red">APAGAR</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- CRIAR NOVO --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> NOVO TREINO
            </h2>
            <form method="POST" action="{{ route('workouts.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <label class="flex flex-col gap-1 md:col-span-2">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">NOME</span>
                    <input name="nome" required maxlength="120" class="vtr-input" placeholder="Ex: Superior A">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">DIA DA SEMANA</span>
                    <select name="dia_semana" class="vtr-input">
                        <option value="">— Sem dia —</option>
                        @foreach($diasLabel as $i => $l)<option value="{{ $i }}">{{ $l }}</option>@endforeach
                    </select>
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">GRUPO MUSCULAR</span>
                    <input name="grupo_muscular" maxlength="120" class="vtr-input" placeholder="Ex: Peito, costas e ombros">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">INTENSIDADE</span>
                    <select name="intensidade" class="vtr-input">
                        <option value="">—</option>
                        <option value="leve">Leve</option>
                        <option value="moderado">Moderado</option>
                        <option value="intenso">Intenso</option>
                    </select>
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">ÍCONE</span>
                    <select name="icone" class="vtr-input">
                        <option value="dumbbell">dumbbell</option>
                        <option value="biceps">biceps</option>
                        <option value="run">run</option>
                        <option value="flame">flame</option>
                        <option value="target">target</option>
                    </select>
                </label>
                <div class="md:col-span-2 flex justify-end">
                    <button class="font-display tracking-[0.22em] text-xs border border-vtr-red text-vtr-red rounded px-4 py-2 hover:bg-vtr-red hover:text-white transition-colors">CRIAR TREINO</button>
                </div>
            </form>
        </div>

    </section>
@endsection
