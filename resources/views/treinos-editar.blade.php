@extends('layouts.shell', [
    'active' => 'treinos',
    'title'  => 'VTR CORE — Editar Treino',
])

@section('shell-content')

    <section class="relative z-10 px-4 md:px-10 mt-8 space-y-5">

        <div class="flex items-center justify-between flex-wrap gap-3">
            <h1 class="font-display tracking-[0.25em] text-2xl md:text-3xl flex items-center gap-3">
                <span class="vtr-divider"></span> EDITAR · {{ mb_strtoupper($workout->nome) }}
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('workouts.manage') }}" class="text-[11px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60">VOLTAR</a>
                @if($workout->dia_semana)
                    <a href="{{ route('treinos', ['dia' => $workout->dia_semana]) }}" class="text-[11px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60">VER NO DIA</a>
                @endif
            </div>
        </div>

        @if(session('ok'))
            <div class="vtr-card border-vtr-red/40 bg-vtr-red/5 px-4 py-2 text-[12px] tracking-[0.18em]">{{ session('ok') }}</div>
        @endif
        @if($errors->any())
            <div class="vtr-card border-vtr-red/40 bg-vtr-red/5 px-4 py-2 text-[12px] text-vtr-red">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        {{-- DADOS DO TREINO --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> DADOS DO TREINO
            </h2>
            <form method="POST" action="{{ route('workouts.update', $workout) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf @method('PUT')
                <label class="flex flex-col gap-1 md:col-span-2">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">NOME</span>
                    <input name="nome" required maxlength="120" value="{{ old('nome', $workout->nome) }}" class="vtr-input">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">DIA DA SEMANA</span>
                    <select name="dia_semana" class="vtr-input">
                        <option value="">— Sem dia —</option>
                        @foreach($diasLabel as $i => $l)<option value="{{ $i }}" @selected(old('dia_semana', $workout->dia_semana) == $i)>{{ $l }}</option>@endforeach
                    </select>
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">GRUPO MUSCULAR</span>
                    <input name="grupo_muscular" maxlength="120" value="{{ old('grupo_muscular', $workout->grupo_muscular) }}" class="vtr-input">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">INTENSIDADE</span>
                    <select name="intensidade" class="vtr-input">
                        @foreach(['' => '—', 'leve' => 'Leve', 'moderado' => 'Moderado', 'intenso' => 'Intenso'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('intensidade', $workout->intensidade) == $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="flex flex-col gap-1">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">ÍCONE</span>
                    <select name="icone" class="vtr-input">
                        @foreach(['dumbbell','biceps','run','flame','target'] as $ic)
                            <option value="{{ $ic }}" @selected(old('icone', $workout->icone) == $ic)>{{ $ic }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="flex items-center gap-2 md:col-span-2">
                    <input type="hidden" name="ativo" value="0">
                    <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $workout->ativo))>
                    <span class="text-[12px] tracking-[0.18em]">ATIVO</span>
                </label>
                <div class="md:col-span-2 flex justify-end">
                    <button class="font-display tracking-[0.22em] text-xs border border-vtr-red text-vtr-red rounded px-4 py-2 hover:bg-vtr-red hover:text-white transition-colors">SALVAR</button>
                </div>
            </form>
        </div>

        {{-- EXERCÍCIOS --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> EXERCÍCIOS
            </h2>

            @if($workout->exercises->isEmpty())
                <p class="text-sm text-vtr-muted mb-4">Nenhum exercício. Adicione abaixo.</p>
            @else
                <ul class="space-y-2 mb-6">
                    @foreach($workout->exercises as $ex)
                        <li class="vtr-card p-4">
                            <details>
                                <summary class="cursor-pointer flex items-center gap-3 flex-wrap">
                                    <span class="w-9 h-9 rounded-full border border-vtr-border grid place-items-center text-vtr-red shrink-0">
                                        @include('partials.icon', ['name' => $ex->icone ?: 'dumbbell', 'class' => 'w-5 h-5'])
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-display text-sm">{{ $ex->ordem }}. {{ $ex->nome }}</div>
                                        <div class="text-[10px] tracking-[0.18em] text-vtr-muted">
                                            {{ $ex->series }}× 
                                            @if($ex->rep_min){{ $ex->rep_min }}@if($ex->rep_max && $ex->rep_max !== $ex->rep_min)–{{ $ex->rep_max }}@endif reps
                                            @elseif($ex->segundos_min){{ $ex->segundos_min }}@if($ex->segundos_max && $ex->segundos_max !== $ex->segundos_min)–{{ $ex->segundos_max }}@endif s
                                            @endif
                                            @if($ex->tipo) · {{ strtoupper($ex->tipo) }} @endif
                                            @if($ex->por_lado) · POR LADO @endif
                                        </div>
                                    </div>
                                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted">EDITAR</span>
                                </summary>
                                <form method="POST" action="{{ route('exercicios.update', $ex) }}" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @csrf @method('PUT')
                                    <label class="flex flex-col gap-1 col-span-2 md:col-span-2">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">NOME</span>
                                        <input name="nome" required maxlength="160" value="{{ $ex->nome }}" class="vtr-input">
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">SÉRIES</span>
                                        <input type="number" name="series" min="1" max="20" required value="{{ $ex->series }}" class="vtr-input">
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">ORDEM</span>
                                        <input type="number" name="ordem" min="0" max="99" value="{{ $ex->ordem }}" class="vtr-input">
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">TIPO</span>
                                        <select name="tipo" class="vtr-input">
                                            @foreach(['forca','cardio','abdomen','aquecimento','mobilidade'] as $t)
                                                <option value="{{ $t }}" @selected($ex->tipo === $t)>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">ÍCONE</span>
                                        <select name="icone" class="vtr-input">
                                            @foreach(['dumbbell','biceps','run','flame','target','clock','leaf'] as $ic)
                                                <option value="{{ $ic }}" @selected($ex->icone === $ic)>{{ $ic }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">REP MIN</span>
                                        <input type="number" name="rep_min" min="0" max="9999" value="{{ $ex->rep_min }}" class="vtr-input">
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">REP MAX</span>
                                        <input type="number" name="rep_max" min="0" max="9999" value="{{ $ex->rep_max }}" class="vtr-input">
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">SEG MIN</span>
                                        <input type="number" name="segundos_min" min="0" max="9999" value="{{ $ex->segundos_min }}" class="vtr-input">
                                    </label>
                                    <label class="flex flex-col gap-1">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">SEG MAX</span>
                                        <input type="number" name="segundos_max" min="0" max="9999" value="{{ $ex->segundos_max }}" class="vtr-input">
                                    </label>
                                    <label class="flex items-center gap-2 col-span-2">
                                        <input type="hidden" name="por_lado" value="0">
                                        <input type="checkbox" name="por_lado" value="1" @checked($ex->por_lado)>
                                        <span class="text-[11px] tracking-[0.18em]">POR LADO</span>
                                    </label>
                                    <label class="flex flex-col gap-1 col-span-2 md:col-span-4">
                                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">OBSERVAÇÃO</span>
                                        <input name="observacao" maxlength="255" value="{{ $ex->observacao }}" class="vtr-input">
                                    </label>
                                    <div class="col-span-2 md:col-span-4 flex justify-between gap-2">
                                        <button class="font-display tracking-[0.22em] text-xs border border-vtr-red text-vtr-red rounded px-4 py-2 hover:bg-vtr-red hover:text-white transition-colors">SALVAR</button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('exercicios.destroy', $ex) }}" class="mt-2" onsubmit="return confirm('Apagar exercício {{ $ex->nome }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60 text-vtr-muted hover:text-vtr-red">APAGAR EXERCÍCIO</button>
                                </form>
                            </details>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- ADICIONAR EXERCÍCIO --}}
            <div class="vtr-card p-4 border-vtr-red/30">
                <h3 class="font-display tracking-[0.22em] text-xs text-vtr-red mb-3">+ NOVO EXERCÍCIO</h3>
                <form method="POST" action="{{ route('exercicios.store', $workout) }}" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @csrf
                    <label class="flex flex-col gap-1 col-span-2 md:col-span-2">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">NOME</span>
                        <input name="nome" required maxlength="160" class="vtr-input" placeholder="Ex: Supino reto">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">SÉRIES</span>
                        <input type="number" name="series" min="1" max="20" required value="3" class="vtr-input">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">TIPO</span>
                        <select name="tipo" class="vtr-input">
                            <option value="forca">forca</option>
                            <option value="cardio">cardio</option>
                            <option value="abdomen">abdomen</option>
                            <option value="aquecimento">aquecimento</option>
                            <option value="mobilidade">mobilidade</option>
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
                            <option value="clock">clock</option>
                        </select>
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">REP MIN</span>
                        <input type="number" name="rep_min" min="0" max="9999" class="vtr-input" placeholder="8">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">REP MAX</span>
                        <input type="number" name="rep_max" min="0" max="9999" class="vtr-input" placeholder="12">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">SEG MIN</span>
                        <input type="number" name="segundos_min" min="0" max="9999" class="vtr-input">
                    </label>
                    <label class="flex flex-col gap-1">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">SEG MAX</span>
                        <input type="number" name="segundos_max" min="0" max="9999" class="vtr-input">
                    </label>
                    <label class="flex flex-col gap-1 col-span-2 md:col-span-3">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted">OBSERVAÇÃO</span>
                        <input name="observacao" maxlength="255" class="vtr-input">
                    </label>
                    <label class="flex items-center gap-2 col-span-2 md:col-span-1">
                        <input type="hidden" name="por_lado" value="0">
                        <input type="checkbox" name="por_lado" value="1">
                        <span class="text-[11px] tracking-[0.18em]">POR LADO</span>
                    </label>
                    <div class="col-span-2 md:col-span-4 flex justify-end">
                        <button class="font-display tracking-[0.22em] text-xs border border-vtr-red text-vtr-red rounded px-4 py-2 hover:bg-vtr-red hover:text-white transition-colors">ADICIONAR</button>
                    </div>
                </form>
            </div>
        </div>

    </section>
@endsection
