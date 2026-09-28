@props(['type' => 'audit'])

<svg {{ $attributes->class(['work-icon']) }} viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($type)
        @case('tax')
            <path d="M25 14h32l14 14v52H25a7 7 0 0 1-7-7V21a7 7 0 0 1 7-7Z"/>
            <path d="M57 14v16h14M31 43h27M31 54h14M31 66h12"/>
            <path d="M60 53h21v23H60zM67 61h7M67 68h7"/>
            @break
        @case('audit')
            <path d="M31 17h34a6 6 0 0 1 6 6v58H25V23a6 6 0 0 1 6-6Z"/>
            <path d="M38 17v-5h20v5M36 39l5 5 9-10M56 41h7M36 61l5 5 9-10M56 63h7"/>
            @break
        @case('code')
            <rect x="14" y="18" width="68" height="48" rx="6"/>
            <path d="M10 74h76M38 74l3-8M58 74l-3-8M38 34l-9 8 9 8M58 34l9 8-9 8M53 31 44 53"/>
            @break
        @case('cloud')
            <path d="M31 67h40a14 14 0 0 0 2-28 24 24 0 0 0-45-5 17 17 0 0 0 3 33Z"/>
            <path d="M47 55v23M36 78h22M58 55l-11-10-11 10"/>
            @break
        @case('support')
            <path d="M22 50v-7a26 26 0 0 1 52 0v7"/>
            <rect x="16" y="47" width="13" height="23" rx="6"/><rect x="67" y="47" width="13" height="23" rx="6"/>
            <path d="M73 70c-1 10-10 14-24 14h-7M42 84h12"/>
            @break
        @case('security')
            <path d="M48 12 76 23v22c0 18-11 29-28 39-17-10-28-21-28-39V23L48 12Z"/>
            <path d="m35 48 9 9 18-20"/>
            @break
        @case('data')
            <ellipse cx="48" cy="23" rx="27" ry="10"/>
            <path d="M21 23v48c0 6 12 11 27 11s27-5 27-11V23M21 39c0 6 12 11 27 11s27-5 27-11M21 55c0 6 12 11 27 11s27-5 27-11"/>
            @break
        @case('growth')
            <path d="M16 78h66M22 65l17-18 13 10 25-29M63 28h14v14"/>
            <rect x="20" y="16" width="19" height="17" rx="3"/><path d="M26 24h7"/>
            @break
        @case('commerce')
            <path d="M23 34h50l-5 47H28l-5-47ZM34 34v-9a14 14 0 0 1 28 0v9"/>
            <path d="M37 57h22M48 46v22"/>
            @break
        @case('design')
            <rect x="17" y="17" width="62" height="62" rx="8"/>
            <path d="m32 62 23-28 9 8-23 27-13 3 4-10ZM53 36l9 8M24 47h15M58 72h12"/>
            @break
        @default
            <path d="M25 14h32l14 14v52H25a7 7 0 0 1-7-7V21a7 7 0 0 1 7-7Z"/>
            <path d="M57 14v16h14M32 45h32M32 58h32M32 71h22"/>
    @endswitch
</svg>
