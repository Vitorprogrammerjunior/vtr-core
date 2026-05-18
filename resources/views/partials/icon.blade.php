@php
    $name = $name ?? 'home';
    $class = $class ?? 'w-5 h-5';
    $sw = 1.6;
@endphp
@switch($name)
    @case('home')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5L12 4l9 7.5"/><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"/></svg>
        @break
    @case('target')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><path d="M12 3v3M21 12h-3M12 21v-3M3 12h3"/></svg>
        @break
    @case('cutlery')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3v8a2 2 0 0 0 2 2v8"/><path d="M11 3v8a2 2 0 0 1-2 2"/><path d="M9 3v6"/><path d="M17 3c-1.5 0-3 2-3 5s1.5 5 3 5v8"/></svg>
        @break
    @case('dumbbell')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9v6M6 7v10M18 7v10M21 9v6"/><path d="M6 12h12"/></svg>
        @break
    @case('note')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h11l3 3v13H5z"/><path d="M16 4v3h3"/><path d="M8 11h8M8 15h6"/></svg>
        @break
    @case('calendar')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
        @break
    @case('chart')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="M7 16l4-5 3 3 5-7"/></svg>
        @break
    @case('gear')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06A2 2 0 1 1 4.29 16.96l.06-.06A1.65 1.65 0 0 0 4.6 15 1.65 1.65 0 0 0 3 14H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 7.04 4.29l.06.06A1.65 1.65 0 0 0 9 4.6 1.65 1.65 0 0 0 10 3v-.09a2 2 0 1 1 4 0V3a1.65 1.65 0 0 0 1 1.6 1.65 1.65 0 0 0 1.82-.33l.06-.06A2 2 0 1 1 19.71 7.04l-.06.06A1.65 1.65 0 0 0 19.4 9c.13.36.4.66.74.83.34.16.71.21 1.07.13H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        @break
    @case('flame')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s4 4 4 8a4 4 0 0 1-8 0c0-1 .5-2 1-3 0 1 1 2 2 2 0-2-1-3-1-5 0-1 1-2 2-2z"/><path d="M8 14a4 4 0 0 0 8 0c0-1-.5-2-1-3"/></svg>
        @break
    @case('book')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5a2 2 0 0 1 2-2h12v18H6a2 2 0 0 1-2-2z"/><path d="M8 7h8M8 11h8M8 15h5"/></svg>
        @break
    @case('bell')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M6 16V11a6 6 0 1 1 12 0v5l1.5 2H4.5z"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>
        @break
    @case('user')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
        @break
    @case('bank')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10l9-6 9 6"/><path d="M5 10v8M9 10v8M15 10v8M19 10v8"/><path d="M3 20h18"/></svg>
        @break
    @case('run')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><circle cx="15" cy="4.5" r="1.6"/><path d="M9 21l3-5-3-3 3-5 4 3 3-1"/><path d="M5 13l3-1 2 2"/></svg>
        @break
    @case('biceps')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M3 14c2-2 5-3 8-3 4 0 6 2 6 5v3H8c-3 0-5-2-5-5z"/><path d="M9 11c0-3 2-5 5-5 3 0 5 2 5 5"/><path d="M11 14c1-1 2-1 3 0"/></svg>
        @break
    @case('brain')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4a3 3 0 0 0-3 3v1a3 3 0 0 0-2 5 3 3 0 0 0 2 3v1a3 3 0 0 0 6 0V4a3 3 0 0 0-3 0z"/><path d="M15 4a3 3 0 0 1 3 3v1a3 3 0 0 1 2 5 3 3 0 0 1-2 3v1a3 3 0 0 1-6 0"/></svg>
        @break
    @case('dollar')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M16 7H10a2.5 2.5 0 0 0 0 5h4a2.5 2.5 0 0 1 0 5H8"/></svg>
        @break
    @case('checklist')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 10l2 2 4-4"/><path d="M8 16h8"/></svg>
        @break
    @case('eye')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
        @break
    @case('plus')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
        @break
    @case('chart-up')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19h16"/><rect x="6" y="13" width="3" height="6"/><rect x="11" y="9" width="3" height="10"/><rect x="16" y="5" width="3" height="14"/><path d="M5 8l4-3 3 2 5-4"/></svg>
        @break
    @case('drop')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s-6 7-6 12a6 6 0 0 0 12 0c0-5-6-12-6-12z"/></svg>
        @break
    @case('cart')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.4 12.3a2 2 0 0 0 2 1.7h7.7a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/></svg>
        @break
    @case('leaf')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M20 4c-9 0-15 5-15 12 0 2 1 4 3 4 7 0 12-6 12-15z"/><path d="M5 20l8-8"/></svg>
        @break
    @case('clock')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        @break
    @case('arrow-right')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $sw }}" class="{{ $class }}"><circle cx="12" cy="12" r="9"/></svg>
@endswitch
