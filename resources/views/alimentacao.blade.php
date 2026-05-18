@extends('layouts.shell', [
    'active'         => 'alimentacao',
    'title'          => 'VTR CORE — Alimentação',
    'modoDisciplina' => $profile->modo_disciplina_on ?? false,
])

@section('shell-content')

    {{-- HERO --}}
    @include('partials.hero', [
        'userName'   => $user->name,
        'heroFrase'  => 'Alimente o corpo. Fortaleça a mente.',
        'heroFoco'   => 'NUTRIÇÃO',
        'heroStatus' => $diaConcluido ? 'CONCLUÍDO' : 'ATIVO',
        'vtrNumber'  => $profile->vtr_number ?? '07',
        'heroPhoto'  => true,
    ])

    @php
        $diaLabels = ['SEG','TER','QUA','QUI','SEX','SÁB','DOM'];
    @endphp

    <section class="relative z-10 px-4 md:px-10 mt-6 space-y-5">

        {{-- ========== Painel de Status do Dia + Hidratação ========== --}}
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
                        <div class="text-[10px] tracking-[0.22em] text-vtr-muted">STATUS HOJE</div>
                        <h2 class="font-display tracking-[0.22em] text-2xl md:text-3xl text-vtr-red mt-1 leading-none">
                            {{ $statusHoje['titulo'] }}
                        </h2>
                        <div class="text-[12px] tracking-[0.18em] text-white/85 mt-2 uppercase">{{ $statusHoje['subtitulo'] }}</div>
                        <p class="text-sm text-vtr-text/85 mt-2">{{ $statusHoje['detalhe'] }}</p>

                        @if($statusHoje['concluido'] && $amanha['primeira'])
                            <div class="mt-4 pt-3 border-t border-vtr-border flex items-center gap-3">
                                <span class="text-vtr-red shrink-0">
                                    @include('partials.icon', ['name' => $amanha['primeira']['icone'], 'class' => 'w-5 h-5'])
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] tracking-[0.22em] text-vtr-muted">PRIMEIRA REFEIÇÃO DE AMANHÃ</div>
                                    <div class="font-display text-sm tracking-[0.18em]">
                                        <span class="text-vtr-red">{{ $amanha['primeira']['horario'] }}</span>
                                        <span class="ml-2">{{ mb_strtoupper($amanha['primeira']['nome']) }}</span>
                                    </div>
                                </div>
                            </div>
                        @elseif(! $statusHoje['concluido'])
                            <div class="vtr-progress mt-4"><span style="width: {{ $statusHoje['percent'] }}%"></span></div>
                        @endif
                    </div>
                </div>

                <blockquote class="mt-4 text-xs text-vtr-muted italic relative pl-3 border-l-2 border-vtr-red/60">
                    “ Disciplina não é o que você sente — é o que você faz. ”
                </blockquote>
            </div>

            {{-- HIDRATAÇÃO --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 relative">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> HIDRATAÇÃO
                </h2>
                <button type="button" data-modal-target="modal-water"
                        class="absolute top-4 right-4 text-[10px] tracking-[0.22em] text-vtr-muted hover:text-vtr-red font-display uppercase transition-colors">
                    Registrar
                </button>

                <div class="font-display text-3xl">{{ number_format($hidratacao['consumido'], 1, ',', '.') }} <span class="text-vtr-muted">/ {{ number_format($hidratacao['meta'], 1, ',', '.') }}</span> <span class="text-vtr-red text-xl">L</span></div>
                <div class="text-[10px] tracking-[0.22em] text-vtr-muted mt-1">META DIÁRIA</div>

                <div class="mt-5 grid grid-cols-5 gap-2">
                    @foreach($hidratacao['copos'] as $cheio)
                        <div class="aspect-[3/4] rounded-sm border border-vtr-border relative overflow-hidden bg-black/40">
                            @if($cheio)
                                <div class="absolute inset-x-0 bottom-0 bg-vtr-red/80" style="height: 88%"></div>
                            @endif
                            <span class="absolute inset-0 grid place-items-center text-vtr-red/40">
                                @include('partials.icon', ['name' => 'drop', 'class' => 'w-4 h-4'])
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="vtr-progress mt-5"><span style="width: {{ $hidratacao['percent'] }}%"></span></div>
                <div class="mt-2 flex items-center justify-between text-[11px]">
                    <span class="flex items-center gap-2 text-vtr-muted tracking-[0.18em]">
                        @include('partials.icon', ['name' => 'drop', 'class' => 'w-3.5 h-3.5'])
                        MANTENHA-SE HIDRATADO
                    </span>
                    <span class="font-display text-vtr-red">{{ $hidratacao['percent'] }}%</span>
                </div>
            </div>
        </div>

        {{-- ========== CARDÁPIO SEMANAL (full width) ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6 relative">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> CARDÁPIO SEMANAL
            </h2>
            <button type="button" data-modal-target="modal-new-meal"
                    class="absolute top-4 right-4 text-[10px] tracking-[0.22em] text-vtr-muted hover:text-vtr-red font-display uppercase transition-colors">
                + Nova Refeição
            </button>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($cardapioSemanal as $dia)
                    <div class="vtr-card p-3 flex flex-col gap-2 {{ $dia['hoje'] ? 'border-vtr-red/70' : '' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-display tracking-[0.22em] text-[11px] {{ $dia['hoje'] ? 'text-vtr-red' : 'text-vtr-muted' }}">{{ $dia['label'] }}</span>
                            @if($dia['hoje'])
                                <span class="text-[9px] tracking-[0.22em] text-vtr-red">HOJE</span>
                            @endif
                        </div>
                        @if(empty($dia['itens']))
                            <p class="text-[11px] text-vtr-muted/70 italic mt-2">Sem refeições</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($dia['itens'] as $item)
                                    <li class="group">
                                        <div class="flex items-start gap-2">
                                            <span class="text-vtr-red shrink-0 mt-0.5">
                                                @include('partials.icon', ['name' => $item['icone'], 'class' => 'w-4 h-4'])
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-baseline gap-1.5">
                                                    <span class="font-display text-vtr-red text-[11px] tracking-[0.18em]">{{ $item['horario'] }}</span>
                                                    @if($item['fixa'])
                                                        <span class="text-[8px] tracking-[0.22em] text-vtr-muted/60" title="Aplica todo dia">★</span>
                                                    @endif
                                                </div>
                                                <div class="text-[12px] leading-tight text-vtr-text">{{ $item['nome'] }}</div>
                                                <div class="text-[10px] text-vtr-muted leading-snug truncate">{{ $item['descricao'] }}</div>
                                            </div>
                                            <div class="flex flex-col gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button type="button" data-modal-target="modal-edit-meal-{{ $item['id'] }}"
                                                        class="text-vtr-muted hover:text-vtr-red text-[10px]" aria-label="Editar">✎</button>
                                                <form method="POST" action="{{ route('refeicoes.destroy', $item['id']) }}"
                                                      data-confirm-modal
                                                      data-confirm-title="Remover refeição?"
                                                      data-confirm-message="A refeição «{{ $item['nome'] }}» será removida do plano."
                                                      data-confirm-ok="Remover">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-vtr-muted hover:text-vtr-red text-[10px]" aria-label="Remover">✕</button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========== Refeições de hoje | Próxima Refeição | Consistência ========== --}}
        <div class="grid grid-cols-1 md:grid-cols-[2.4fr_1fr_1.2fr] gap-5">

            {{-- REFEIÇÕES DE HOJE --}}
            <div class="vtr-card vtr-corner p-5 md:p-6">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> REFEIÇÕES DE HOJE
                </h2>

                @if(empty($refeicoes))
                    <p class="text-vtr-muted text-sm italic">Nenhuma refeição planejada para hoje.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        @foreach($refeicoes as $r)
                            <form method="POST" action="{{ route('refeicoes.toggle', $r['id']) }}"
                                  class="vtr-card p-3 flex flex-col items-center text-center gap-2 hover:border-vtr-red/60 transition-colors w-full {{ $r['feita'] ? 'border-vtr-red/40 bg-vtr-red/5' : '' }}">
                                @csrf
                                <div class="text-[10px] tracking-[0.22em] uppercase text-vtr-muted">{{ $r['nome'] }}</div>
                                <div class="font-display text-vtr-red text-sm tracking-[0.18em]">{{ $r['horario'] }}</div>
                                <span class="my-1 text-vtr-red">
                                    @include('partials.icon', ['name' => $r['icone'], 'class' => 'w-9 h-9'])
                                </span>
                                <div class="text-[11px] leading-tight text-vtr-text/85 min-h-[32px]">{{ $r['descricao'] }}</div>
                                <button type="submit"
                                        class="vtr-check {{ $r['feita'] ? '' : 'empty' }} mt-1 w-6 h-6 cursor-pointer hover:border-vtr-red transition-colors"
                                        aria-label="Marcar refeição">
                                    @if($r['feita'])
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- PRÓXIMA REFEIÇÃO --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 flex flex-col">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> {{ $diaConcluido ? 'AMANHÃ' : 'PRÓXIMA REFEIÇÃO' }}
                </h2>
                <div class="flex flex-col items-center text-center flex-1 justify-center">
                    @if($diaConcluido && $amanha['primeira'])
                        <div class="w-12 h-12 rounded-full border-2 border-vtr-red/70 grid place-items-center mb-3">
                            @include('partials.icon', ['name' => $amanha['primeira']['icone'], 'class' => 'w-5 h-5 text-vtr-red'])
                        </div>
                        <div class="font-display text-3xl">{{ $amanha['primeira']['horario'] ?: '--:--' }}</div>
                        <div class="text-vtr-red font-display text-xs tracking-[0.25em] mt-1">{{ mb_strtoupper($amanha['primeira']['nome']) }}</div>
                        <div class="text-[12px] text-vtr-text/85 mt-2">{{ $amanha['primeira']['descricao'] }}</div>
                        <div class="text-[10px] tracking-[0.22em] text-vtr-muted mt-3 font-display uppercase">{{ $amanha['label'] }}</div>
                    @elseif($diaConcluido)
                        <div class="w-12 h-12 rounded-full border-2 border-vtr-red/70 grid place-items-center mb-3">
                            @include('partials.icon', ['name' => 'checklist', 'class' => 'w-5 h-5 text-vtr-red'])
                        </div>
                        <div class="font-display text-vtr-red text-sm tracking-[0.25em]">DESCANSE.</div>
                        <div class="text-[12px] text-vtr-text/85 mt-2">Sem refeições planejadas para amanhã.</div>
                    @else
                        <div class="w-12 h-12 rounded-full border-2 border-vtr-red/70 grid place-items-center mb-3">
                            @include('partials.icon', ['name' => 'clock', 'class' => 'w-5 h-5 text-vtr-red'])
                        </div>
                        <div class="font-display text-3xl">{{ $proximaRefeicao['horario'] }}</div>
                        <div class="text-vtr-red font-display text-xs tracking-[0.25em] mt-1">{{ $proximaRefeicao['titulo'] }}</div>
                        <div class="text-[12px] text-vtr-text/85 mt-2">{{ $proximaRefeicao['descricao'] }}</div>
                    @endif
                </div>
                @if(! $diaConcluido && $proximaRefeicao['id'] && ! $proximaRefeicao['feita'])
                    <form method="POST" action="{{ route('refeicoes.toggle', $proximaRefeicao['id']) }}" class="mt-4">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display border border-vtr-red/60 rounded px-3 py-2 text-vtr-red hover:bg-vtr-red/10 transition-colors">
                            <span>MARCAR COMO FEITA</span>
                            <span>✓</span>
                        </button>
                    </form>
                @endif
            </div>

            {{-- CONSISTÊNCIA ALIMENTAR --}}
            <div class="vtr-card vtr-corner p-5 md:p-6">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> CONSISTÊNCIA ALIMENTAR
                </h2>
                <div class="flex items-center gap-4">
                    <div class="vtr-ring shrink-0" style="--val: {{ $consistencia['percent'] }}; width:120px; height:120px;">
                        <div>
                            <div class="font-display text-2xl text-vtr-red leading-none">{{ $consistencia['percent'] }}%</div>
                            <div class="text-[9px] tracking-[0.18em] text-vtr-muted mt-1">{{ $consistencia['rotulo'] }}</div>
                        </div>
                    </div>
                    <ul class="flex-1 space-y-1.5 text-[12px]">
                        @foreach($consistencia['itens'] as $it)
                            <li class="flex items-center gap-2">
                                <span class="vtr-check {{ $it['feito'] ? '' : 'empty' }} w-4 h-4 shrink-0">
                                    @if($it['feito'])
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @endif
                                </span>
                                <span class="{{ $it['feito'] ? 'text-white' : 'text-vtr-muted' }}">{{ $it['titulo'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-4 text-[10px] tracking-[0.22em] text-vtr-muted">
                    <span class="font-display text-vtr-red text-base">{{ $consistencia['feitas'] }} / {{ $consistencia['total'] }}</span>
                    ITENS CONCLUÍDOS HOJE
                </div>
            </div>
        </div>

        {{-- ========== Ações Rápidas ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> AÇÕES RÁPIDAS
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <button type="button" data-modal-target="modal-new-meal"
                        class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                    <span class="w-10 h-10 rounded-full border border-vtr-border grid place-items-center text-vtr-red shrink-0">
                        @include('partials.icon', ['name' => 'cutlery', 'class' => 'w-5 h-5'])
                    </span>
                    <span class="font-display tracking-[0.18em] text-[11px] uppercase">Nova Refeição</span>
                </button>
                <button type="button" data-modal-target="modal-water"
                        class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                    <span class="w-10 h-10 rounded-full border border-vtr-border grid place-items-center text-vtr-red shrink-0">
                        @include('partials.icon', ['name' => 'drop', 'class' => 'w-5 h-5'])
                    </span>
                    <span class="font-display tracking-[0.18em] text-[11px] uppercase">Registrar Água</span>
                </button>
                <a href="{{ route('dashboard') }}"
                   class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                    <span class="w-10 h-10 rounded-full border border-vtr-border grid place-items-center text-vtr-red shrink-0">
                        @include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5'])
                    </span>
                    <span class="font-display tracking-[0.18em] text-[11px] uppercase">Visão Geral</span>
                </a>
            </div>
        </div>

        {{-- ========== Frase rodapé ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6 flex items-center gap-4">
            <div class="text-vtr-red text-3xl font-display leading-none">“</div>
            <p class="flex-1 text-sm md:text-base text-vtr-text/90 italic">
                Disciplina na alimentação é escolher o futuro todos os dias.
            </p>
            <div class="font-display tracking-[0.25em] text-xs text-vtr-muted hidden md:block">VTR <span class="text-vtr-red">NUTRI</span></div>
        </div>
    </section>

    {{-- ========== MODAIS ========== --}}

    {{-- Nova refeição --}}
    <x-form-modal id="modal-new-meal" title="Nova Refeição" icon="cutlery" :action="route('refeicoes.store')" okLabel="Adicionar">
        <label class="block">
            <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Nome</span>
            <input type="text" name="nome" required maxlength="80" placeholder="Ex.: Pré-treino" class="vtr-input mt-1.5">
        </label>
        <div class="grid grid-cols-2 gap-3">
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Horário</span>
                <input type="time" name="horario" class="vtr-input mt-1.5">
            </label>
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Ícone</span>
                <select name="icone" class="vtr-input mt-1.5">
                    <option value="cutlery">Talher</option>
                    <option value="leaf">Folha</option>
                    <option value="drop">Gota</option>
                </select>
            </label>
        </div>
        <label class="block">
            <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Dia da semana</span>
            <select name="dia_semana" class="vtr-input mt-1.5">
                <option value="">Todos os dias</option>
                @foreach($diaLabels as $i => $l)
                    <option value="{{ $i }}">{{ ucfirst(strtolower($l)) }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Descrição</span>
            <input type="text" name="descricao" maxlength="160" placeholder="Ex.: Tapioca + ovos" class="vtr-input mt-1.5">
        </label>
    </x-form-modal>

    {{-- Editar refeições (uma única vez por id) --}}
    @php $editIds = []; @endphp
    @foreach($cardapioSemanal as $dia)
        @foreach($dia['itens'] as $item)
            @if(in_array($item['id'], $editIds, true)) @continue @endif
            @php $editIds[] = $item['id']; @endphp
            <x-form-modal :id="'modal-edit-meal-' . $item['id']" title="Editar Refeição" icon="cutlery"
                          :action="route('refeicoes.update', $item['id'])" method="PUT" okLabel="Salvar">
                <label class="block">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Nome</span>
                    <input type="text" name="nome" required maxlength="80" value="{{ $item['nome'] }}" class="vtr-input mt-1.5">
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Horário</span>
                        <input type="time" name="horario" value="{{ $item['horario'] }}" class="vtr-input mt-1.5">
                    </label>
                    <label class="block">
                        <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Ícone</span>
                        <select name="icone" class="vtr-input mt-1.5">
                            @foreach(['cutlery'=>'Talher','leaf'=>'Folha','drop'=>'Gota'] as $val => $label)
                                <option value="{{ $val }}" @selected($item['icone'] === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <label class="block">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Dia da semana</span>
                    <select name="dia_semana" class="vtr-input mt-1.5">
                        <option value="" @selected($item['fixa'])>Todos os dias</option>
                        @foreach($diaLabels as $i => $l)
                            <option value="{{ $i }}" @selected(! $item['fixa'] && $dia['idx'] === $i)>{{ ucfirst(strtolower($l)) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Descrição</span>
                    <input type="text" name="descricao" maxlength="160" value="{{ $item['descricao'] }}" class="vtr-input mt-1.5">
                </label>
            </x-form-modal>
        @endforeach
    @endforeach

    {{-- Registrar água --}}
    <x-form-modal id="modal-water" title="Registrar Hidratação" icon="drop" :action="route('agua.store')" okLabel="Salvar">
        <label class="block">
            <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Litros consumidos hoje</span>
            <input type="number" step="0.1" min="0" max="10" name="litros" required
                   value="{{ number_format($hidratacao['consumido'], 1, '.', '') }}" class="vtr-input mt-1.5">
            <p class="text-[10px] text-vtr-muted mt-1.5">Meta: {{ number_format($hidratacao['meta'], 1, ',', '.') }} L · cada copo ≈ 0,3 L</p>
        </label>
    </x-form-modal>

@endsection
