{{--
    VTR CORE — Form modal partial (estilo padrão site).
    Props:
      $id        (string, required)  ID HTML único
      $title     (string)            Título (uppercase)
      $icon      (string)            Nome do ícone em partials.icon
      $action    (string, required)  URL do form (route)
      $method    (string)            POST | PUT | DELETE | PATCH (default POST)
      $okLabel   (string)            Texto do botão primário (default 'Salvar')
      $slot      (Blade slot)        Conteúdo do form (campos)
--}}
@props([
    'id',
    'title' => 'Confirmar',
    'icon'  => 'target',
    'action',
    'method' => 'POST',
    'okLabel' => 'Salvar',
])

@php
    $verb = strtoupper($method);
    $useSpoof = in_array($verb, ['PUT', 'PATCH', 'DELETE']);
@endphp

<div id="{{ $id }}" class="vtr-modal-backdrop">
    <form method="POST" action="{{ $action }}" class="vtr-modal" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        @csrf
        @if($useSpoof)
            <input type="hidden" name="_method" value="{{ $verb }}">
        @endif
        <span class="vtr-modal-glow"></span>
        <button type="button" class="vtr-modal-close" data-modal-close aria-label="Fechar">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M6 6L18 18M18 6L6 18" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="vtr-modal-icon">
            @include('partials.icon', ['name' => $icon, 'class' => 'w-6 h-6'])
        </div>
        <h3 id="{{ $id }}-title" class="vtr-modal-title">{{ $title }}</h3>
        <div class="vtr-modal-body space-y-3">
            {{ $slot }}
        </div>
        <div class="vtr-modal-actions">
            <button type="button" class="vtr-btn vtr-btn-ghost" data-modal-close>Cancelar</button>
            <button type="submit" class="vtr-btn">{{ $okLabel }}</button>
        </div>
    </form>
</div>
