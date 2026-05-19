@extends('layouts.shell', [
    'active'         => 'anotacoes',
    'title'          => 'VTR CORE — Anotações',
    'modoDisciplina' => $profile->modo_disciplina_on ?? false,
])

@push('styles')
<style>
[x-cloak] { display: none !important; }

/* ===== Markdown renderer ===== */
.prose-vtr { line-height: 1.75; font-size: 0.875rem; color: rgba(244,244,245,0.88); }
.prose-vtr > *:first-child { margin-top: 0; }
.prose-vtr h1 { font-family: var(--font-display); font-size: 1.5rem; letter-spacing: 0.05em;
    color: var(--color-vtr-text); margin: 1.5rem 0 0.75rem; }
.prose-vtr h2 { font-family: var(--font-display); font-size: 1.15rem; letter-spacing: 0.05em;
    color: var(--color-vtr-text); margin: 1.25rem 0 0.5rem; }
.prose-vtr h3 { font-size: 0.65rem; letter-spacing: 0.22em; text-transform: uppercase;
    color: var(--color-vtr-muted); margin: 1.25rem 0 0.5rem; }
.prose-vtr h3::before { content: "// "; color: var(--color-vtr-red); }
.prose-vtr p { margin: 0.75rem 0; }
.prose-vtr ul, .prose-vtr ol { padding-left: 1.25rem; margin: 0.75rem 0; }
.prose-vtr li { margin: 0.35rem 0; }
.prose-vtr ul > li { list-style: none; display: flex; align-items: flex-start; gap: 0.625rem; }
.prose-vtr ul > li::before { content: ""; flex-shrink: 0; width: 6px; height: 6px;
    border-radius: 50%; background: var(--color-vtr-red); margin-top: 0.55rem; }
.prose-vtr ol { list-style: decimal; }
.prose-vtr pre { background: rgba(255,255,255,0.04); border: 1px solid var(--color-vtr-border);
    border-radius: 0.5rem; padding: 1rem; margin: 1rem 0; overflow-x: auto; }
.prose-vtr pre code { font-family: monospace; font-size: 0.75rem; line-height: 1.65;
    color: var(--color-vtr-text); background: none; padding: 0; border-radius: 0; }
.prose-vtr :not(pre) > code { background: rgba(255,255,255,0.08); padding: 0.1em 0.35em;
    border-radius: 0.25rem; font-size: 0.8rem; font-family: monospace; }
.prose-vtr blockquote { border-left: 2px solid var(--color-vtr-red); padding-left: 1rem;
    margin: 1rem 0; color: rgba(244,244,245,0.6); font-style: italic; }
.prose-vtr a { color: var(--color-vtr-red); text-decoration: underline; }
.prose-vtr strong { color: var(--color-vtr-text); font-weight: 600; }
.prose-vtr hr { border: none; border-top: 1px solid var(--color-vtr-border); margin: 1.5rem 0; }
.prose-vtr table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.8rem; }
.prose-vtr th, .prose-vtr td { padding: 0.5rem 0.75rem; border: 1px solid var(--color-vtr-border); }
.prose-vtr th { background: rgba(230,0,18,0.08); font-family: var(--font-display); letter-spacing: 0.1em; }
</style>
@endpush

@section('shell-content')
<section
    class="relative z-10 px-4 md:px-10 pt-6 md:pt-10"
    x-data="anotacoes()"
    x-cloak
>
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl md:text-4xl text-vtr-red tracking-[0.05em]">ANOTAÇÕES</h1>
            <p class="mt-1 text-[11px] md:text-xs tracking-[0.22em] text-vtr-muted uppercase">
                Organize seus estudos. Crie conhecimento. Evolua todos os dias.
            </p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="mt-6 flex flex-col md:flex-row gap-3 md:items-center">
        <label class="vtr-card flex items-center gap-2 px-3 py-2 flex-1 md:max-w-md">
            <span class="text-vtr-muted shrink-0">
                @include('partials.icon', ['name' => 'eye', 'class' => 'w-4 h-4'])
            </span>
            <input type="text" placeholder="Buscar livros e anotações..."
                   x-model="search"
                   class="bg-transparent outline-none text-sm w-full placeholder:text-vtr-muted/70" />
        </label>
        <div class="md:ml-auto">
            <button type="button" @click="openModal('newBook')"
                    class="bg-vtr-red hover:bg-vtr-red/90 text-white px-5 py-2.5 rounded flex items-center gap-2 font-display tracking-[0.22em] text-xs transition-colors">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
                NOVO BOOK
            </button>
        </div>
    </div>

    {{-- Grid: Books | Editor | Páginas --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-[1fr_2fr_1fr] gap-5">

        {{-- ============ MEUS BOOKS ============ --}}
        <div class="vtr-card vtr-corner p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display tracking-[0.22em] text-xs">MEUS BOOKS</h2>
                <span class="text-[10px] tracking-[0.18em] text-vtr-muted"
                      x-text="books.length + (books.length === 1 ? ' BOOK' : ' BOOKS')"></span>
            </div>

            <div x-show="filteredBooks.length === 0"
                 class="flex-1 flex flex-col items-center justify-center py-10 text-center gap-3">
                @include('partials.icon', ['name' => 'book', 'class' => 'w-8 h-8 text-vtr-muted/40 mx-auto'])
                <p class="text-[11px] tracking-[0.18em] text-vtr-muted"
                   x-text="books.length === 0 ? 'NENHUM BOOK CRIADO' : 'NENHUM RESULTADO'"></p>
            </div>

            <ul x-show="filteredBooks.length > 0" class="space-y-2 flex-1">
                <template x-for="b in filteredBooks" :key="b.id">
                    <li>
                        <button type="button" @click="selectBook(b)"
                                :class="activeBook?.id === b.id
                                    ? 'border-vtr-red ring-1 ring-vtr-red/40'
                                    : 'hover:border-vtr-red/50'"
                                class="w-full text-left vtr-card p-3 flex items-center gap-3 transition-colors">
                            <span :class="activeBook?.id === b.id ? 'bg-vtr-red/15 text-vtr-red' : 'text-vtr-muted'"
                                  class="w-9 h-9 rounded grid place-items-center shrink-0">
                                @include('partials.icon', ['name' => 'book', 'class' => 'w-5 h-5'])
                            </span>
                            <span class="flex-1 min-w-0">
                                <span :class="activeBook?.id === b.id ? 'text-vtr-red' : 'text-white'"
                                      class="block font-display tracking-[0.12em] text-sm truncate"
                                      x-text="b.titulo.toUpperCase()"></span>
                                <span class="block text-[10px] tracking-[0.18em] text-vtr-muted mt-0.5"
                                      x-text="b.paginas_total + ' PÁGINAS'"></span>
                            </span>
                        </button>
                    </li>
                </template>
            </ul>

            <button type="button" @click="openModal('newBook')"
                    class="mt-5 w-full flex items-center justify-center gap-2 text-[11px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2.5 hover:border-vtr-red/60 transition-colors">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
                CRIAR NOVO BOOK
            </button>
        </div>

        {{-- ============ EDITOR ============ --}}
        <div class="vtr-card vtr-corner p-5 md:p-6 flex flex-col min-h-[480px]">

            <div x-show="!activeBook"
                 class="flex-1 flex flex-col items-center justify-center py-20 text-center gap-4">
                @include('partials.icon', ['name' => 'note', 'class' => 'w-12 h-12 text-vtr-muted/30 mx-auto'])
                <p class="font-display tracking-[0.22em] text-sm text-vtr-muted">NENHUM BOOK SELECIONADO</p>
                <p class="text-[11px] text-vtr-muted/60 tracking-wider">Crie um book para começar a anotar</p>
                <button @click="openModal('newBook')"
                        class="mt-2 bg-vtr-red hover:bg-vtr-red/90 text-white px-5 py-2.5 rounded font-display tracking-[0.22em] text-xs transition-colors">
                    CRIAR PRIMEIRO BOOK
                </button>
            </div>

            <div x-show="activeBook" class="flex flex-col flex-1">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 rounded grid place-items-center bg-vtr-red/10 text-vtr-red shrink-0">
                        @include('partials.icon', ['name' => 'book', 'class' => 'w-5 h-5'])
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="font-display text-xl tracking-[0.05em]"
                             x-text="activeBook?.titulo?.toUpperCase()"></div>
                        <div class="flex items-center gap-2 mt-1 text-[10px] tracking-[0.18em] text-vtr-muted">
                            <span class="w-1.5 h-1.5 rounded-full bg-vtr-red inline-block"></span>
                            <span x-text="(activeBook?.paginas_total ?? 0) + ' PÁGINAS'"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 text-vtr-muted shrink-0">
                        <button @click="openEditBook()"
                                class="w-8 h-8 grid place-items-center hover:text-vtr-red transition-colors" title="Editar book">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button @click="confirmDeleteBook()"
                                class="w-8 h-8 grid place-items-center hover:text-vtr-red transition-colors" title="Excluir book">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <hr class="my-5 border-vtr-border" />

                <div x-show="!activePage"
                     class="flex-1 flex flex-col items-center justify-center py-12 text-center gap-3">
                    @include('partials.icon', ['name' => 'note', 'class' => 'w-8 h-8 text-vtr-muted/30 mx-auto'])
                    <p class="text-[11px] tracking-[0.18em] text-vtr-muted">NENHUMA PÁGINA CRIADA</p>
                    <button @click="openModal('newPage')"
                            class="text-[11px] tracking-[0.18em] text-vtr-red hover:text-vtr-red/80 font-display transition-colors">
                        + CRIAR PRIMEIRA PÁGINA
                    </button>
                </div>

                <div x-show="activePage" class="flex flex-col flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-[10px] tracking-[0.22em] text-vtr-muted">PÁGINA ATUAL</div>
                            <h3 class="mt-1 font-display text-2xl md:text-3xl"
                                x-text="activePage ? activePage.numero + '. ' + activePage.titulo.toUpperCase() : ''"></h3>
                        </div>
                        <button @click="openEditPage()"
                                class="w-8 h-8 grid place-items-center text-vtr-muted hover:text-vtr-red transition-colors shrink-0 mt-1" title="Editar página">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>

                    <div x-show="pageLoading" class="mt-5 flex items-center gap-2 text-[11px] text-vtr-muted">
                        <svg class="animate-spin w-4 h-4 text-vtr-red" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/>
                            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                        <span>Carregando...</span>
                    </div>

                    <div x-show="!pageLoading"
                         x-html="renderedContent"
                         class="mt-4 prose-vtr flex-1 overflow-auto"></div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3 text-[10px] tracking-[0.22em] text-vtr-muted border-t border-vtr-border pt-4">
                        <div>CRIADO EM: <span class="text-white" x-text="activeBook?.criado_em ?? '—'"></span></div>
                        <div>ÚLTIMA EDIÇÃO: <span class="text-white" x-text="activeBook?.ultima_edicao ?? '—'"></span></div>
                        <div>STATUS: <span class="text-vtr-red" x-text="activeBook?.status_label ?? '—'"></span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PÁGINAS ============ --}}
        <div class="vtr-card vtr-corner p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display tracking-[0.22em] text-xs">PÁGINAS</h2>
                <span class="text-[10px] tracking-[0.18em] text-vtr-muted"
                      x-text="pages.length + ' PÁGINAS'"></span>
            </div>

            <div x-show="pages.length === 0"
                 class="flex-1 flex items-center justify-center py-10 text-center">
                <p class="text-[11px] tracking-[0.18em] text-vtr-muted"
                   x-text="activeBook ? 'NENHUMA PÁGINA' : 'SELECIONE UM BOOK'"></p>
            </div>

            <ul x-show="pages.length > 0"
                class="space-y-1.5 flex-1 max-h-[640px] overflow-auto pr-1">
                <template x-for="p in pages" :key="p.id">
                    <li>
                        <button type="button" @click="selectPage(p)"
                                :class="activePage?.id === p.id
                                    ? 'bg-vtr-red/10 text-vtr-red border-vtr-red/40'
                                    : 'border-transparent hover:bg-white/5 text-vtr-text/85'"
                                class="w-full text-left flex items-center justify-between gap-2 px-3 py-2.5 rounded text-sm transition-colors border group">
                            <span class="font-display tracking-[0.05em] truncate flex-1"
                                  x-text="p.numero + '. ' + p.titulo.toUpperCase()"></span>
                            <span x-show="activePage?.id === p.id && !$el.closest('.group:hover')"
                                  class="w-1.5 h-1.5 rounded-full bg-vtr-red shrink-0"></span>
                            <span class="opacity-0 group-hover:opacity-100 flex items-center gap-1 shrink-0 transition-all">
                                <button @click.stop="openEditPageFromList(p)"
                                        class="w-5 h-5 grid place-items-center text-vtr-muted hover:text-vtr-red transition-colors"
                                        title="Editar página">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <button @click.stop="confirmDeletePage(p)"
                                        class="w-5 h-5 grid place-items-center text-vtr-muted hover:text-vtr-red transition-colors"
                                        title="Excluir página">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </span>
                        </button>
                    </li>
                </template>
            </ul>

            <button type="button" @click="openModal('newPage')"
                    :disabled="!activeBook"
                    :class="!activeBook ? 'opacity-40 cursor-not-allowed' : 'hover:border-vtr-red/60'"
                    class="mt-5 w-full flex items-center justify-center gap-2 text-[11px] tracking-[0.22em] font-display border border-vtr-border rounded px-3 py-2.5 transition-colors">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
                NOVA PÁGINA
            </button>
        </div>
    </div>

    {{-- ========== MODAIS ========== --}}

    {{-- Modal: Novo Book --}}
    <div x-cloak x-show="modals.newBook"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="closeModal('newBook')" @keydown.escape.window="closeModal('newBook')"
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="vtr-card w-full max-w-md p-6 relative">
            <button @click="closeModal('newBook')" class="absolute top-4 right-4 text-vtr-muted hover:text-white transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
            <h3 class="font-display tracking-[0.22em] text-sm mb-5">NOVO BOOK</h3>
            <form @submit.prevent="createBook">
                <label class="block text-[10px] tracking-[0.22em] text-vtr-muted mb-1.5">TÍTULO DO BOOK</label>
                <input x-model="forms.newBook.titulo" x-ref="newBookInput"
                       type="text" placeholder="Ex: JavaScript Avançado" maxlength="255"
                       class="w-full vtr-card px-3 py-2.5 text-sm outline-none focus:border-vtr-red/70 transition-colors"
                       required />
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="closeModal('newBook')"
                            class="flex-1 vtr-card px-4 py-2.5 text-[11px] tracking-[0.22em] font-display hover:border-vtr-red/50 transition-colors">
                        CANCELAR
                    </button>
                    <button type="submit" :disabled="busy"
                            class="flex-1 bg-vtr-red hover:bg-vtr-red/90 text-white px-4 py-2.5 text-[11px] tracking-[0.22em] font-display rounded transition-colors disabled:opacity-60">
                        <span x-text="busy ? 'CRIANDO...' : 'CRIAR BOOK'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Editar Book --}}
    <div x-cloak x-show="modals.editBook"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="closeModal('editBook')" @keydown.escape.window="closeModal('editBook')"
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="vtr-card w-full max-w-md p-6 relative">
            <button @click="closeModal('editBook')" class="absolute top-4 right-4 text-vtr-muted hover:text-white transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
            <h3 class="font-display tracking-[0.22em] text-sm mb-5">EDITAR BOOK</h3>
            <form @submit.prevent="updateBook">
                <label class="block text-[10px] tracking-[0.22em] text-vtr-muted mb-1.5">TÍTULO</label>
                <input x-model="forms.editBook.titulo" type="text" maxlength="255"
                       class="w-full vtr-card px-3 py-2.5 text-sm outline-none focus:border-vtr-red/70 transition-colors"
                       required />
                <label class="block text-[10px] tracking-[0.22em] text-vtr-muted mb-1.5 mt-4">STATUS</label>
                <select x-model="forms.editBook.status"
                        class="w-full bg-[#0c0c0c] border border-vtr-border rounded-[10px] px-3 py-2.5 text-sm outline-none focus:border-vtr-red transition-colors text-vtr-text">
                    <option value="em_andamento">EM ANDAMENTO</option>
                    <option value="pausado">PAUSADO</option>
                    <option value="concluido">CONCLUÍDO</option>
                </select>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="closeModal('editBook')"
                            class="flex-1 vtr-card px-4 py-2.5 text-[11px] tracking-[0.22em] font-display hover:border-vtr-red/50 transition-colors">
                        CANCELAR
                    </button>
                    <button type="submit" :disabled="busy"
                            class="flex-1 bg-vtr-red hover:bg-vtr-red/90 text-white px-4 py-2.5 text-[11px] tracking-[0.22em] font-display rounded transition-colors disabled:opacity-60">
                        <span x-text="busy ? 'SALVANDO...' : 'SALVAR'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Nova Página --}}
    <div x-cloak x-show="modals.newPage"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="closeModal('newPage')" @keydown.escape.window="closeModal('newPage')"
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="vtr-card w-full max-w-lg p-6 relative">
            <button @click="closeModal('newPage')" class="absolute top-4 right-4 text-vtr-muted hover:text-white transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
            <h3 class="font-display tracking-[0.22em] text-sm mb-5">NOVA PÁGINA</h3>
            <form @submit.prevent="createPage">
                <label class="block text-[10px] tracking-[0.22em] text-vtr-muted mb-1.5">TÍTULO DA PÁGINA</label>
                <input x-model="forms.newPage.titulo" x-ref="newPageInput"
                       type="text" placeholder="Ex: Introdução ao JavaScript" maxlength="255"
                       class="w-full vtr-card px-3 py-2.5 text-sm outline-none focus:border-vtr-red/70 transition-colors"
                       required />
                <label class="block text-[10px] tracking-[0.22em] text-vtr-muted mb-1.5 mt-4">
                    CONTEÚDO <span class="text-vtr-muted/60 normal-case font-sans tracking-normal">(markdown)</span>
                </label>
                <textarea x-model="forms.newPage.conteudo" rows="7"
                          placeholder="# Título&#10;&#10;Escreva aqui em **markdown**..."
                          class="w-full vtr-card px-3 py-2.5 text-sm outline-none focus:border-vtr-red/70 transition-colors font-mono resize-none leading-relaxed"></textarea>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="closeModal('newPage')"
                            class="flex-1 vtr-card px-4 py-2.5 text-[11px] tracking-[0.22em] font-display hover:border-vtr-red/50 transition-colors">
                        CANCELAR
                    </button>
                    <button type="submit" :disabled="busy"
                            class="flex-1 bg-vtr-red hover:bg-vtr-red/90 text-white px-4 py-2.5 text-[11px] tracking-[0.22em] font-display rounded transition-colors disabled:opacity-60">
                        <span x-text="busy ? 'CRIANDO...' : 'CRIAR PÁGINA'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Editar Página --}}
    <div x-cloak x-show="modals.editPage"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click.self="closeModal('editPage')" @keydown.escape.window="closeModal('editPage')"
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="vtr-card w-full max-w-2xl p-6 relative">
            <button @click="closeModal('editPage')" class="absolute top-4 right-4 text-vtr-muted hover:text-white transition-colors">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/></svg>
            </button>
            <h3 class="font-display tracking-[0.22em] text-sm mb-5">EDITAR PÁGINA</h3>
            <form @submit.prevent="updatePage">
                <label class="block text-[10px] tracking-[0.22em] text-vtr-muted mb-1.5">TÍTULO</label>
                <input x-model="forms.editPage.titulo" type="text" maxlength="255"
                       class="w-full vtr-card px-3 py-2.5 text-sm outline-none focus:border-vtr-red/70 transition-colors"
                       required />
                <label class="block text-[10px] tracking-[0.22em] text-vtr-muted mb-1.5 mt-4">
                    CONTEÚDO <span class="text-vtr-muted/60 normal-case font-sans tracking-normal">(markdown)</span>
                </label>
                <textarea x-model="forms.editPage.conteudo" rows="14"
                          class="w-full vtr-card px-3 py-2.5 text-sm outline-none focus:border-vtr-red/70 transition-colors font-mono resize-y leading-relaxed"></textarea>
                <div class="flex gap-3 mt-5">
                    <button type="button" @click="closeModal('editPage')"
                            class="flex-1 vtr-card px-4 py-2.5 text-[11px] tracking-[0.22em] font-display hover:border-vtr-red/50 transition-colors">
                        CANCELAR
                    </button>
                    <button type="submit" :disabled="busy"
                            class="flex-1 bg-vtr-red hover:bg-vtr-red/90 text-white px-4 py-2.5 text-[11px] tracking-[0.22em] font-display rounded transition-colors disabled:opacity-60">
                        <span x-text="busy ? 'SALVANDO...' : 'SALVAR ALTERAÇÕES'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked@14/marked.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('anotacoes', () => ({
        books:      @json($books),
        activeBook: @json($activeBook),
        pages:      @json($pages),
        activePage: @json($activePage),

        search:      '',
        busy:        false,
        pageLoading: false,

        modals: { newBook: false, editBook: false, newPage: false, editPage: false },
        forms:  {
            newBook:  { titulo: '' },
            editBook: { titulo: '', status: 'em_andamento' },
            newPage:  { titulo: '', conteudo: '' },
            editPage: { titulo: '', conteudo: '' },
        },

        init() {
            if (window.marked) marked.setOptions({ breaks: true });
        },

        get filteredBooks() {
            if (!this.search.trim()) return this.books;
            const q = this.search.toLowerCase();
            return this.books.filter(b => b.titulo.toLowerCase().includes(q));
        },

        get renderedContent() {
            if (!this.activePage?.conteudo) {
                return '<p style="color:var(--color-vtr-muted);font-size:0.875rem">Página vazia — clique no lápis para adicionar conteúdo.</p>';
            }
            const html = window.marked ? marked.parse(this.activePage.conteudo) : '<pre>' + this.activePage.conteudo + '</pre>';
            return window.DOMPurify ? DOMPurify.sanitize(html) : html;
        },

        openModal(name) {
            this.modals[name] = true;
            this.$nextTick(() => {
                const ref = { newBook: 'newBookInput', newPage: 'newPageInput' }[name];
                if (ref && this.$refs[ref]) this.$refs[ref].focus();
            });
        },
        closeModal(name) { this.modals[name] = false; },

        openEditBook() {
            this.forms.editBook.titulo = this.activeBook.titulo;
            this.forms.editBook.status = this.activeBook.status;
            this.openModal('editBook');
        },
        openEditPage() {
            this.forms.editPage.titulo   = this.activePage.titulo;
            this.forms.editPage.conteudo = this.activePage.conteudo ?? '';
            this.openModal('editPage');
        },
        async openEditPageFromList(page) {
            if (page.id !== this.activePage?.id) {
                await this.selectPage(page);
            }
            this.openEditPage();
        },

        async apiFetch(url, method = 'GET', data = null) {
            const opts = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            };
            if (data !== null) opts.body = JSON.stringify(data);
            const res = await fetch(url, opts);
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                if (err.errors) {
                    const first = Object.values(err.errors)[0]?.[0] || 'Dados inválidos';
                    throw new Error(first);
                }
                throw new Error(err.message || 'Erro na requisição');
            }
            const text = await res.text();
            return text ? JSON.parse(text) : {};
        },

        statusLabel(s) {
            return { em_andamento: 'EM ANDAMENTO', pausado: 'PAUSADO', concluido: 'CONCLUÍDO' }[s] ?? s.toUpperCase();
        },

        async selectBook(book) {
            if (this.activeBook?.id === book.id) return;
            this.activeBook = book;
            this.pages = [];
            this.activePage = null;
            this.pageLoading = true;
            try {
                const data = await this.apiFetch(`/anotacoes/books/${book.id}/paginas`);
                this.pages      = data.pages;
                this.activePage = data.activePage;
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            } finally {
                this.pageLoading = false;
            }
        },

        async selectPage(page) {
            if (this.activePage?.id === page.id) return;
            this.pageLoading = true;
            try {
                const data = await this.apiFetch(`/anotacoes/paginas/${page.id}`);
                this.activePage = data;
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            } finally {
                this.pageLoading = false;
            }
        },

        async createBook() {
            if (this.busy) return;
            this.busy = true;
            try {
                const book = await this.apiFetch('/anotacoes/books', 'POST', { titulo: this.forms.newBook.titulo });
                this.books.push(book);
                this.closeModal('newBook');
                this.forms.newBook.titulo = '';
                await this.selectBook(book);
                window.vtrToast?.('Book criado!', 'info');
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            } finally {
                this.busy = false;
            }
        },

        async updateBook() {
            if (this.busy || !this.activeBook) return;
            this.busy = true;
            try {
                const updated = await this.apiFetch(`/anotacoes/books/${this.activeBook.id}`, 'PUT', {
                    titulo: this.forms.editBook.titulo,
                    status: this.forms.editBook.status,
                });
                const idx = this.books.findIndex(b => b.id === this.activeBook.id);
                if (idx !== -1) Object.assign(this.books[idx], updated);
                Object.assign(this.activeBook, updated);
                this.closeModal('editBook');
                window.vtrToast?.('Book atualizado!', 'info');
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            } finally {
                this.busy = false;
            }
        },

        confirmDeleteBook() {
            window.vtrConfirm?.({
                title:   'Excluir book?',
                message: 'Todas as páginas serão excluídas. Esta ação não pode ser desfeita.',
                okLabel: 'EXCLUIR',
                onOk:    () => this.deleteBook(),
            });
        },

        async deleteBook() {
            if (!this.activeBook) return;
            const deletedId = this.activeBook.id;
            try {
                await this.apiFetch(`/anotacoes/books/${deletedId}`, 'DELETE');
                this.books = this.books.filter(b => b.id !== deletedId);
                this.pages = [];
                this.activePage = null;
                this.activeBook = this.books[0] ?? null;
                if (this.activeBook) await this.selectBook(this.activeBook);
                window.vtrToast?.('Book excluído.', 'info');
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            }
        },

        async createPage() {
            if (this.busy || !this.activeBook) return;
            this.busy = true;
            try {
                const page = await this.apiFetch(`/anotacoes/books/${this.activeBook.id}/paginas`, 'POST', {
                    titulo:   this.forms.newPage.titulo,
                    conteudo: this.forms.newPage.conteudo || null,
                });
                this.pages.push(page);
                this.activeBook.paginas_total = this.pages.length;
                const bIdx = this.books.findIndex(b => b.id === this.activeBook.id);
                if (bIdx !== -1) this.books[bIdx].paginas_total = this.pages.length;
                this.activePage = page;
                this.closeModal('newPage');
                this.forms.newPage.titulo = '';
                this.forms.newPage.conteudo = '';
                window.vtrToast?.('Página criada!', 'info');
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            } finally {
                this.busy = false;
            }
        },

        async updatePage() {
            if (this.busy || !this.activePage) return;
            this.busy = true;
            try {
                await this.apiFetch(`/anotacoes/paginas/${this.activePage.id}`, 'PUT', {
                    titulo:   this.forms.editPage.titulo,
                    conteudo: this.forms.editPage.conteudo,
                });
                this.activePage.titulo   = this.forms.editPage.titulo;
                this.activePage.conteudo = this.forms.editPage.conteudo;
                const idx = this.pages.findIndex(p => p.id === this.activePage.id);
                if (idx !== -1) this.pages[idx].titulo = this.forms.editPage.titulo;
                this.closeModal('editPage');
                window.vtrToast?.('Página atualizada!', 'info');
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            } finally {
                this.busy = false;
            }
        },

        confirmDeletePage(page) {
            window.vtrConfirm?.({
                title:   'Excluir página?',
                message: `"${page.titulo}" será excluída permanentemente.`,
                okLabel: 'EXCLUIR',
                onOk:    () => this.deletePage(page),
            });
        },

        async deletePage(page) {
            const deletedId = page.id;
            try {
                await this.apiFetch(`/anotacoes/paginas/${deletedId}`, 'DELETE');
                this.pages = this.pages.filter(p => p.id !== deletedId);
                this.activeBook.paginas_total = this.pages.length;
                const bIdx = this.books.findIndex(b => b.id === this.activeBook.id);
                if (bIdx !== -1) this.books[bIdx].paginas_total = this.pages.length;
                if (this.activePage?.id === deletedId) {
                    this.activePage = null;
                    if (this.pages.length > 0) await this.selectPage(this.pages[0]);
                }
                window.vtrToast?.('Página excluída.', 'info');
            } catch (e) {
                window.vtrToast?.(e.message, 'error');
            }
        },
    }));
});
</script>

@endpush
@endsection