<header class="md:hidden flex items-center justify-between px-4 pt-4">
    <div class="flex items-center gap-2">
        <div class="vtr-logo w-9 h-9">@include('partials.logo')</div>
        <div class="font-display tracking-[0.25em] text-sm">VTR <span class="text-vtr-red">CORE</span></div>
    </div>
    <div class="flex items-center gap-3">
        <button class="relative w-10 h-10 rounded-full border border-vtr-border flex items-center justify-center">
            @include('partials.icon', ['name' => 'bell', 'class' => 'w-5 h-5'])
            <span class="absolute -top-0.5 -right-0.5 bg-vtr-red text-[10px] rounded-full w-4 h-4 grid place-items-center">3</span>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-10 h-10 rounded-full border border-vtr-border flex items-center justify-center">
                @include('partials.icon', ['name' => 'user', 'class' => 'w-5 h-5'])
            </button>
        </form>
    </div>
</header>
