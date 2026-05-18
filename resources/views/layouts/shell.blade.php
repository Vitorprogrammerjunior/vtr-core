@php
    /**
     * Layout interno autenticado.
     * Vars:
     *   - $active   (string) chave do item ativo do nav
     *   - $title    (string|null)
     *   - $modoDisciplina (bool)
     */
    $nav = [
        ['key' => 'inicio',      'label' => 'Início',        'icon' => 'home',     'href' => route('dashboard')],
        ['key' => 'objetivos',   'label' => 'Objetivos',     'icon' => 'target',   'href' => route('objetivos')],
        ['key' => 'alimentacao', 'label' => 'Alimentação',   'icon' => 'cutlery',  'href' => route('alimentacao')],
        ['key' => 'treinos',     'label' => 'Treinos',       'icon' => 'dumbbell', 'href' => route('treinos')],
        ['key' => 'anotacoes',   'label' => 'Anotações',     'icon' => 'note',     'href' => route('anotacoes')],
        ['key' => 'config',      'label' => 'Configurações', 'icon' => 'gear',     'href' => '#'],
    ];
@endphp

@extends('layouts.app', ['title' => $title ?? 'VTR CORE'])

@section('content')
<div class="md:flex md:min-h-dvh">
    @include('partials.sidebar', ['nav' => $nav, 'active' => $active ?? null, 'modoDisciplina' => $modoDisciplina ?? false])

    <div class="flex-1 pb-24 md:pb-0">
        @include('partials.mobile-topbar')
        {{ $slot ?? '' }}
        @yield('shell-content')
        <div class="h-8 md:h-10"></div>
    </div>
</div>

@include('partials.bottom-nav', ['nav' => $nav, 'active' => $active ?? null, 'modoDisciplina' => $modoDisciplina ?? false])

{{-- Flash → toast (consumido pelo runtime em resources/js/app.js) --}}
@if(session('status'))
    <span data-flash="{{ session('status') }}" data-flash-type="info"></span>
@endif
@if(session('error'))
    <span data-flash="{{ session('error') }}" data-flash-type="error"></span>
@endif
@if($errors->any())
    <span data-flash="{{ $errors->first() }}" data-flash-type="error"></span>
@endif
@endsection
