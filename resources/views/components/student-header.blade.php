<header class="sticky top-0 z-20 h-[73px] border-b border-[#C4C6CF]/30 bg-[#F7F9FF]/90 shadow-[0_1px_8px_rgba(11,35,69,0.04)] backdrop-blur-[24px]">
    <div class="mx-auto flex h-[72px] w-full max-w-[1440px] items-center justify-between gap-6 px-4 sm:px-10 lg:px-[60px]">
        <div class="flex items-center gap-6">
            <a class="flex shrink-0 items-center gap-2 text-2xl font-bold tracking-[-.5px] text-[#000D25]" href="{{ route('dashboard') }}"><img class="h-12 w-12 object-contain" src="{{ asset('images/assets/logoradix.png') }}" alt=""><span>Radix</span></a>
            <nav class="hidden items-center gap-1 text-sm font-semibold text-[#44474E] md:flex" aria-label="Navigasi utama">
                <a class="rounded-full px-4 py-2 {{ request()->routeIs('dashboard') ? 'bg-[#000D25] text-white' : 'hover:bg-slate-100' }}" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="rounded-full px-4 py-2 {{ request()->routeIs('sql-lab.*') ? 'bg-[#000D25] text-white' : 'hover:bg-slate-100' }}" href="{{ route('sql-lab.index') }}">SQL Lab</a>
                <a class="rounded-full px-4 py-2 {{ request()->routeIs('leaderboard.*') ? 'bg-[#000D25] text-white' : 'hover:bg-slate-100' }}" href="{{ route('leaderboard.index') }}">Leaderboard</a>
                <a class="rounded-full px-4 py-2 {{ request()->routeIs('profile.*') ? 'bg-[#000D25] text-white' : 'hover:bg-slate-100' }}" href="{{ route('profile.show') }}">Profil</a>
            </nav>
        </div>
        <div class="flex items-center gap-4">
            <span class="hidden items-center gap-1.5 rounded-full border border-[#006B59]/20 bg-[#69FAD8] px-3.5 py-1.5 font-mono text-[13px] font-semibold text-[#002019] shadow-[0_0_12px_rgba(25,195,164,0.2)] sm:inline-flex">ϟ {{ number_format(auth()->user()->total_xp) }} XP</span>@if (auth()->user()->isAdmin())<a class="hidden rounded-lg border border-[#C4C6CF] px-3 py-2 text-sm font-semibold text-[#44474E] hover:bg-white sm:inline-flex {{ request()->routeIs('admin.*') ? 'bg-white text-[#000D25]' : '' }}" href="{{ route('admin.dashboard') }}">Admin</a>@endif<a class="flex h-10 w-10 items-center justify-center rounded-full border border-[#C4C6CF] bg-white p-0.5 shadow-sm" href="{{ route('profile.show') }}"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#000D25]"><img class="h-7 w-7 rounded-full object-cover" src="{{ asset('images/assets/avatar.png') }}" alt="Profil {{ auth()->user()->name }}"></span></a>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="hidden rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 xl:block">Keluar</button></form>
        </div>
    </div>
</header>
@if (session('status'))<div class="mx-auto mt-4 max-w-6xl px-4 sm:px-6"><div class="rounded-xl bg-teal-50 p-3 text-sm text-teal-800" role="status">{{ session('status') }}</div></div>@endif
@if ($errors->any())<div class="mx-auto mt-4 max-w-6xl px-4 sm:px-6"><div class="rounded-xl bg-red-50 p-3 text-sm text-red-800" role="alert">{{ $errors->first() }}</div></div>@endif
