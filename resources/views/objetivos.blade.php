@extends('layouts.shell', [
    'active'         => 'objetivos',
    'title'          => 'VTR CORE — Objetivos',
    'modoDisciplina' => $profile->modo_disciplina_on ?? false,
])

@section('shell-content')

    {{-- HERO --}}
    @include('partials.hero', [
        'userName'   => $user->name,
        'heroFrase'  => 'Clareza nas metas. Ação todos os dias.',
        'heroFoco'   => 'OBJETIVOS',
        'heroStatus' => 'ATIVO',
        'vtrNumber'  => $profile->vtr_number ?? '07',
        'heroPhoto'  => true,
    ])

    {{-- ========== Linha 1: Meta principal | Metas ativas | Consistência ========== --}}
    <section class="relative z-10 px-4 md:px-10 mt-6 space-y-5">

        <div class="grid grid-cols-1 md:grid-cols-[1.1fr_1.1fr_0.9fr] gap-5">

            {{-- META PRINCIPAL --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 relative">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> META PRINCIPAL
                </h2>
                @if($metaPrincipal['id'])
                    <button type="button" data-modal-target="modal-edit-main" class="absolute top-4 right-4 text-[10px] tracking-[0.22em] text-vtr-muted hover:text-vtr-red font-display uppercase transition-colors">Editar</button>
                @endif
                <div class="flex items-start gap-3 mb-2">
                    @include('partials.icon', ['name' => 'target', 'class' => 'w-7 h-7 text-vtr-red mt-1'])
                    <div>
                        <h3 class="font-display text-2xl md:text-3xl">{{ mb_strtoupper($metaPrincipal['titulo']) }}</h3>
                        <p class="text-[11px] tracking-[0.22em] text-vtr-muted mt-1 uppercase">Meta até {{ $metaPrincipal['prazo'] }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <div class="vtr-progress flex-1"><span style="width: {{ $metaPrincipal['progresso'] }}%"></span></div>
                    <div class="font-display text-vtr-red text-2xl">{{ $metaPrincipal['progresso'] }}%</div>
                </div>
                <blockquote class="mt-4 text-xs text-vtr-muted italic relative pl-3 border-l-2 border-vtr-red/60">
                    “ {{ $metaPrincipal['frase'] }} ”
                </blockquote>
            </div>

            {{-- METAS ATIVAS --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 relative">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> METAS ATIVAS
                </h2>
                <button type="button" data-modal-target="modal-new-meta" class="absolute top-4 right-4 text-[10px] tracking-[0.22em] text-vtr-muted hover:text-vtr-red font-display uppercase transition-colors">+ Nova</button>
                @if(empty($metasAtivas))
                    <p class="text-vtr-muted text-sm italic">Nenhuma meta ativa. Adicione a primeira.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($metasAtivas as $meta)
                            <li class="flex items-center gap-3 group">
                                <span class="w-7 h-7 grid place-items-center text-vtr-red shrink-0">
                                    @include('partials.icon', ['name' => $meta['icone'], 'class' => 'w-5 h-5'])
                                </span>
                                <span class="text-sm flex-1 min-w-[110px]">{{ $meta['titulo'] }}</span>
                                <div class="vtr-progress flex-[2] max-w-[140px]"><span style="width: {{ $meta['progresso'] }}%"></span></div>
                                <span class="font-display text-vtr-red text-base w-12 text-right">{{ $meta['progresso'] }}%</span>
                                <button type="button" data-modal-target="modal-edit-meta-{{ $meta['id'] }}"
                                        class="opacity-0 group-hover:opacity-100 text-vtr-muted hover:text-vtr-red transition-all text-xs"
                                        aria-label="Editar meta">✎</button>
                                <form method="POST" action="{{ route('metas.destroy', $meta['id']) }}"
                                      data-confirm-modal
                                      data-confirm-title="Remover meta?"
                                      data-confirm-message="A meta «{{ $meta['titulo'] }}» será removida."
                                      data-confirm-ok="Remover">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="opacity-0 group-hover:opacity-100 text-vtr-muted hover:text-vtr-red transition-all text-xs" aria-label="Remover meta">✕</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- CONSISTÊNCIA --}}
            <div class="vtr-card vtr-corner p-5 md:p-6">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> CONSISTÊNCIA
                </h2>
                <div class="flex items-center gap-4">
                    <div class="vtr-ring shrink-0" style="--val: {{ $consistencia['percent'] }}; width:120px; height:120px;">
                        <div>
                            <div class="font-display text-2xl text-white">{{ $consistencia['percent'] }}%</div>
                            <div class="text-[9px] tracking-[0.18em] text-vtr-muted mt-0.5">{{ $consistencia['dias'] }} DIAS SEGUIDOS</div>
                        </div>
                    </div>
                    <svg viewBox="0 0 100 40" class="flex-1 h-[100px]" preserveAspectRatio="none">
                        <path d="M0 32 L10 28 L20 30 L30 22 L40 24 L50 18 L60 16 L72 18 L82 10 L92 8 L100 4"
                              stroke="#e60012" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <circle cx="100" cy="4" r="2" fill="#e60012"/>
                        <path d="M0 32 L10 28 L20 30 L30 22 L40 24 L50 18 L60 16 L72 18 L82 10 L92 8 L100 4 L100 40 L0 40 Z"
                              fill="url(#consgrad)" opacity="0.35"/>
                        <defs>
                            <linearGradient id="consgrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#e60012"/>
                                <stop offset="100%" stop-color="transparent"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>

        {{-- ========== Linha 2: Áreas de evolução | Próximo marco | Hábitos-chave ========== --}}
        <div class="grid grid-cols-1 md:grid-cols-[2.2fr_0.9fr_0.9fr] gap-5">

            {{-- ÁREAS DE EVOLUÇÃO --}}
            <div class="vtr-card vtr-corner p-5 md:p-6">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> ÁREAS DE EVOLUÇÃO
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($areas as $area)
                        @php $temDado = $area['progresso'] !== null; @endphp
                        <div class="vtr-card p-4 flex flex-col">
                            <div class="flex items-start gap-2 mb-2">
                                <span class="text-vtr-red shrink-0">
                                    @include('partials.icon', ['name' => $area['icone'], 'class' => 'w-7 h-7'])
                                </span>
                                <div>
                                    <div class="font-display text-lg leading-tight">{{ mb_strtoupper($area['titulo']) }}</div>
                                    @if($area['count'] !== null)
                                        <div class="text-[9px] tracking-[0.18em] text-vtr-muted mt-0.5 uppercase">{{ $area['count'] }} {{ $area['count'] === 1 ? 'meta' : 'metas' }}</div>
                                    @endif
                                </div>
                            </div>
                            <p class="text-[11px] text-vtr-muted leading-snug min-h-[32px]">{{ $area['desc'] }}</p>
                            <div class="mt-3 flex items-center gap-2">
                                @if($temDado)
                                    <span class="font-display text-vtr-red text-base w-10">{{ $area['progresso'] }}%</span>
                                    <div class="vtr-progress flex-1"><span style="width: {{ $area['progresso'] }}%"></span></div>
                                @else
                                    <span class="font-display text-vtr-muted text-base w-10">—</span>
                                    <div class="text-[10px] tracking-[0.18em] text-vtr-muted/70 uppercase flex-1">Sem metas nesta área</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- PRÓXIMO PASSO (hoje) --}}
            <div class="vtr-card vtr-corner p-5 md:p-6 flex flex-col">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> PRÓXIMO PASSO
                </h2>

                @if($proximoPasso['total'] === 0)
                    {{-- sem hábitos cadastrados --}}
                    <div class="flex flex-col items-center text-center flex-1 justify-center">
                        <div class="w-14 h-14 rounded-full border-2 border-vtr-muted/40 grid place-items-center mb-3 text-vtr-muted">
                            @include('partials.icon', ['name' => 'plus', 'class' => 'w-6 h-6'])
                        </div>
                        <div class="font-display text-base tracking-[0.05em] text-vtr-muted">SEM HÁBITOS</div>
                        <p class="text-[11px] text-vtr-muted mt-2 leading-snug px-2">Cadastre o primeiro hábito para começar a rotina.</p>
                    </div>
                    <button type="button" data-modal-target="modal-new-habito"
                            class="mt-4 w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2 hover:border-vtr-red/60 transition-colors">
                        <span>ADICIONAR HÁBITO</span>
                        <span>+</span>
                    </button>

                @elseif($proximoPasso['allDone'])
                    {{-- tudo feito hoje --}}
                    <div class="flex flex-col items-center text-center flex-1 justify-center">
                        <div class="w-14 h-14 rounded-full border-2 border-vtr-red grid place-items-center mb-3 text-vtr-red">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="font-display text-xl tracking-[0.05em]">DIA COMPLETO</div>
                        <div class="text-vtr-red font-display text-sm mt-2">{{ $proximoPasso['feitos'] }}/{{ $proximoPasso['total'] }} hábitos feitos</div>
                        <p class="text-[11px] text-vtr-muted mt-2 leading-snug">Volta amanhã. Não quebre a cadeia.</p>
                    </div>

                @else
                    {{-- próximo hábito a marcar --}}
                    @php $next = $proximoPasso['next']; @endphp
                    <div class="flex flex-col items-center text-center flex-1 justify-center">
                        <div class="w-14 h-14 rounded-full border-2 border-vtr-red/70 grid place-items-center mb-3">
                            @include('partials.icon', ['name' => 'checklist', 'class' => 'w-6 h-6 text-vtr-red'])
                        </div>
                        <div class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Próximo hábito</div>
                        <div class="font-display text-lg tracking-[0.05em] mt-1 leading-tight">{{ mb_strtoupper($next['titulo']) }}</div>
                        <div class="text-vtr-red font-display text-sm mt-3">
                            {{ $proximoPasso['feitos'] }}/{{ $proximoPasso['total'] }} feitos hoje
                        </div>
                        <div class="vtr-progress w-full mt-2">
                            <span style="width: {{ $proximoPasso['total'] > 0 ? round(($proximoPasso['feitos'] / $proximoPasso['total']) * 100) : 0 }}%"></span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('habitos.toggle', $next['id']) }}" class="mt-4">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-between text-[11px] tracking-[0.22em] font-display border border-vtr-red/60 rounded px-3 py-2 text-vtr-red hover:bg-vtr-red/10 transition-colors">
                            <span>MARCAR COMO FEITO</span>
                            <span>✓</span>
                        </button>
                    </form>
                @endif
            </div>

            {{-- HÁBITOS-CHAVE --}}
            <div class="vtr-card vtr-corner p-5 md:p-6">
                <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                    <span class="vtr-divider"></span> HÁBITOS-CHAVE
                </h2>

                @if(session('status'))
                    <div class="mb-3 text-[11px] tracking-[0.18em] text-vtr-red uppercase hidden">{{ session('status') }}</div>
                @endif

                <ul class="space-y-2.5">
                    @foreach($habitos as $h)
                        <li class="flex items-center gap-3 text-sm group">
                            <form method="POST" action="{{ route('habitos.toggle', $h['id']) }}" class="flex items-center gap-3 flex-1">
                                @csrf
                                <button type="submit" class="vtr-check {{ $h['feito'] ? '' : 'empty' }} w-5 h-5 cursor-pointer hover:border-vtr-red transition-colors" aria-label="Marcar hábito">
                                    @if($h['feito'])
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @endif
                                </button>
                                <button type="submit" class="text-left flex-1 {{ $h['feito'] ? 'text-white' : 'text-vtr-muted' }} hover:text-white transition-colors">
                                    {{ $h['titulo'] }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('habitos.destroy', $h['id']) }}"
                                  data-confirm-modal
                                  data-confirm-title="Remover hábito?"
                                  data-confirm-message="O hábito «{{ $h['titulo'] }}» será removido."
                                  data-confirm-ok="Remover">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="opacity-0 group-hover:opacity-100 text-vtr-muted hover:text-vtr-red transition-all text-xs" aria-label="Remover hábito">✕</button>
                            </form>
                        </li>
                    @endforeach
                </ul>

                <button type="button" data-modal-target="modal-new-habito"
                        class="mt-5 w-full vtr-card hover:border-vtr-red/60 transition-colors px-3 py-2 font-display tracking-[0.18em] text-[11px] uppercase text-vtr-red">
                    + Adicionar hábito
                </button>
            </div>
        </div>

        {{-- ========== Ações rápidas ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6">
            <h2 class="font-display tracking-[0.25em] text-sm flex items-center gap-3 mb-5">
                <span class="vtr-divider"></span> AÇÕES RÁPIDAS
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button type="button" data-modal-target="modal-new-meta" class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                    @include('partials.icon', ['name' => 'target', 'class' => 'w-5 h-5 text-vtr-red'])
                    <span class="font-display tracking-[0.18em] text-[11px] uppercase">Nova Meta</span>
                </button>
                @if($metaPrincipal['id'])
                    <button type="button" data-modal-target="modal-edit-main" class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                        @include('partials.icon', ['name' => 'chart-up', 'class' => 'w-5 h-5 text-vtr-red'])
                        <span class="font-display tracking-[0.18em] text-[11px] uppercase">Registrar Progresso</span>
                    </button>
                @endif
                <button type="button" data-modal-target="modal-new-habito" class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                    @include('partials.icon', ['name' => 'plus', 'class' => 'w-5 h-5 text-vtr-red'])
                    <span class="font-display tracking-[0.18em] text-[11px] uppercase">Adicionar Hábito</span>
                </button>
                <a href="{{ route('dashboard') }}" class="vtr-card hover:border-vtr-red/60 transition-colors p-4 flex items-center gap-3 text-left">
                    @include('partials.icon', ['name' => 'eye', 'class' => 'w-5 h-5 text-vtr-red'])
                    <span class="font-display tracking-[0.18em] text-[11px] uppercase">Ver Visão Geral</span>
                </a>
            </div>
        </div>

        {{-- ========== Frase rodapé ========== --}}
        <div class="vtr-card vtr-corner p-5 md:p-6 flex items-center gap-4">
            <div class="text-vtr-red text-3xl font-display leading-none">“</div>
            <p class="flex-1 text-sm md:text-base text-vtr-text/90 italic">
                Objetivo sem ação é só intenção.
            </p>
            <div class="font-display tracking-[0.25em] text-xs text-vtr-muted hidden md:block">VTR <span class="text-vtr-red">GOALS</span></div>
        </div>
    </section>

    {{-- ========== MODAIS ========== --}}

    {{-- Nova meta --}}
    <x-form-modal id="modal-new-meta" title="Nova Meta" icon="target" :action="route('metas.store')" okLabel="Adicionar">
        <label class="block">
            <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Título</span>
            <input type="text" name="titulo" required maxlength="120" placeholder="Ex.: Ler 12 livros"
                   class="vtr-input mt-1.5">
        </label>
        <label class="block">
            <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Categoria / Ícone</span>
            <select name="icone" class="vtr-input mt-1.5">
                <option value="target">Geral</option>
                <option value="dumbbell">Físico</option>
                <option value="run">Corrida</option>
                <option value="biceps">Força</option>
                <option value="book">Leitura / Mente</option>
                <option value="bank">Financeiro</option>
                <option value="dollar">Dinheiro</option>
                <option value="heart">Saúde</option>
                <option value="checklist">Rotina</option>
            </select>
        </label>
        <div class="grid grid-cols-2 gap-3">
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Progresso (%)</span>
                <input type="number" name="progresso" min="0" max="100" value="0" class="vtr-input mt-1.5">
            </label>
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Prazo</span>
                <input type="date" name="prazo" class="vtr-input mt-1.5">
            </label>
        </div>
    </x-form-modal>

    {{-- Edit meta principal --}}
    @if($metaPrincipal['id'])
        <x-form-modal id="modal-edit-main" title="Editar Meta Principal" icon="target"
                      :action="route('metas.update', $metaPrincipal['id'])" method="PUT" okLabel="Salvar">
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Título</span>
                <input type="text" name="titulo" required maxlength="120"
                       value="{{ $metaPrincipal['titulo'] }}" class="vtr-input mt-1.5">
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="block">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Progresso (%)</span>
                    <input type="number" name="progresso" min="0" max="100"
                           value="{{ $metaPrincipal['progresso'] }}" class="vtr-input mt-1.5">
                </label>
                <label class="block">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Prazo</span>
                    <input type="date" name="prazo" value="{{ $metaPrincipal['prazo_iso'] }}" class="vtr-input mt-1.5">
                </label>
            </div>
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Frase / Lembrete</span>
                <textarea name="frase" rows="2" maxlength="240" class="vtr-input mt-1.5">{{ $metaPrincipal['frase'] }}</textarea>
            </label>
        </x-form-modal>
    @endif

    {{-- Adicionar hábito --}}
    <x-form-modal id="modal-new-habito" title="Novo Hábito" icon="plus" :action="route('habitos.store')" okLabel="Adicionar">
        <label class="block">
            <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Título</span>
            <input type="text" name="titulo" required maxlength="120" placeholder="Ex.: Beber 3L de água"
                   class="vtr-input mt-1.5">
        </label>
    </x-form-modal>

    {{-- Editar cada meta ativa --}}
    @foreach($metasAtivas as $meta)
        <x-form-modal :id="'modal-edit-meta-' . $meta['id']" title="Editar Meta" icon="target"
                      :action="route('metas.update', $meta['id'])" method="PUT" okLabel="Salvar">
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Título</span>
                <input type="text" name="titulo" required maxlength="120" value="{{ $meta['titulo'] }}" class="vtr-input mt-1.5">
            </label>
            <label class="block">
                <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Categoria / Ícone</span>
                <select name="icone" class="vtr-input mt-1.5">
                    @foreach(['target'=>'Geral','dumbbell'=>'Físico','run'=>'Corrida','biceps'=>'Força','book'=>'Leitura / Mente','bank'=>'Financeiro','dollar'=>'Dinheiro','heart'=>'Saúde','checklist'=>'Rotina'] as $val => $label)
                        <option value="{{ $val }}" @selected($meta['categoria'] === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="block">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Progresso (%)</span>
                    <input type="number" name="progresso" min="0" max="100" value="{{ $meta['progresso'] }}" class="vtr-input mt-1.5">
                </label>
                <label class="block">
                    <span class="text-[10px] tracking-[0.22em] text-vtr-muted uppercase">Prazo</span>
                    <input type="date" name="prazo" value="{{ $meta['prazo_iso'] }}" class="vtr-input mt-1.5">
                </label>
            </div>
        </x-form-modal>
    @endforeach

@endsection
