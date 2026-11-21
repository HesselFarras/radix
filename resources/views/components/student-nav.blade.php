<nav class="fixed inset-x-0 bottom-0 z-20 grid grid-cols-4 border-t border-slate-200 bg-white/95 px-1 py-1.5 text-center text-[11px] backdrop-blur md:hidden sm:p-2 sm:text-xs" aria-label="Navigasi mobile">
    <a class="rounded-lg py-2 {{ request()->routeIs('dashboard') ? 'bg-teal-50 font-semibold text-teal-700' : 'text-slate-600' }}" href="{{ route('dashboard') }}"><span class="block text-base">⌂</span>Dashboard</a>
    <a class="rounded-lg py-2 {{ request()->routeIs('sql-lab.*') ? 'bg-teal-50 font-semibold text-teal-700' : 'text-slate-600' }}" href="{{ route('sql-lab.index') }}"><span class="block text-base">⌘</span>SQL Lab</a>
    <a class="rounded-lg py-2 {{ request()->routeIs('leaderboard.*') ? 'bg-teal-50 font-semibold text-teal-700' : 'text-slate-600' }}" href="{{ route('leaderboard.index') }}"><span class="block text-base">♜</span>Ranking</a>
    <a class="rounded-lg py-2 {{ request()->routeIs('profile.*') ? 'bg-teal-50 font-semibold text-teal-700' : 'text-slate-600' }}" href="{{ route('profile.show') }}"><span class="block text-base">●</span>Profil</a>
</nav>
