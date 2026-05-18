@php
    $groups = $groups ?? [];
    $hl = fn($g) => in_array($g, $groups, true) ? '#e60012' : '#1a1a1a';
@endphp
<svg viewBox="0 0 120 180" class="w-28 h-44" fill="none">
    <defs>
        <linearGradient id="bodyG" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#262626"/>
            <stop offset="100%" stop-color="#0d0d0d"/>
        </linearGradient>
        <radialGradient id="hlPeito" cx="50%" cy="40%" r="60%">
            <stop offset="0%" stop-color="#ff1a2b" stop-opacity="0.95"/>
            <stop offset="100%" stop-color="#8a0008" stop-opacity="0.7"/>
        </radialGradient>
    </defs>
    {{-- silhueta base --}}
    <ellipse cx="60" cy="20" rx="13" ry="14" fill="url(#bodyG)"/>
    <path d="M40 38 Q60 30 80 38 L88 70 Q92 90 90 110 Q88 122 84 130 L36 130 Q32 122 30 110 Q28 90 32 70 Z" fill="url(#bodyG)"/>
    <path d="M30 50 Q22 70 22 95 L26 130 L36 130 L36 70 Z" fill="url(#bodyG)"/>
    <path d="M90 50 Q98 70 98 95 L94 130 L84 130 L84 70 Z" fill="url(#bodyG)"/>
    <path d="M40 130 L42 175 L56 175 L58 130 Z" fill="url(#bodyG)"/>
    <path d="M62 130 L64 175 L78 175 L80 130 Z" fill="url(#bodyG)"/>

    {{-- peitorais --}}
    <path d="M44 46 Q60 40 76 46 Q72 60 60 64 Q48 60 44 46 Z" fill="{{ in_array('peito', $groups, true) ? 'url(#hlPeito)' : '#1f1f1f' }}"/>
    {{-- abdomen --}}
    <path d="M52 66 L68 66 L66 100 L54 100 Z" fill="#181818"/>
    <path d="M54 70 L66 70 M54 78 L66 78 M54 86 L66 86 M54 94 L66 94" stroke="#0a0a0a" stroke-width="1"/>
    {{-- triceps --}}
    <ellipse cx="26" cy="80" rx="6" ry="14" fill="{{ $hl('triceps') }}"/>
    <ellipse cx="94" cy="80" rx="6" ry="14" fill="{{ $hl('triceps') }}"/>
    {{-- pernas detalhe --}}
    <ellipse cx="49" cy="150" rx="8" ry="20" fill="{{ $hl('pernas') }}" opacity="0.8"/>
    <ellipse cx="71" cy="150" rx="8" ry="20" fill="{{ $hl('pernas') }}" opacity="0.8"/>
</svg>
