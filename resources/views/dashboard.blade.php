<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Radix</title>
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#0b2644">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-100 pb-20 text-slate-900 md:pb-6">
    <header class="border-b bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between p-4">
            <a class="text-xl font-bold" href="{{ route('dashboard') }}">radix<span class="text-teal-600">.</span></a>
            <nav class="hidden gap-5 text-sm md:flex" aria-label="Navigasi utama"><a href="{{ route('dashboard') }}">Dashboard</a><a href="{{ route('leaderboard.index') }}">Leaderboard</a><a href="{{ route('profile.show') }}">Profil</a></nav>
            <span class="rounded-full bg-teal-100 px-4 py-2 text-sm font-semibold text-teal-900">⚡ {{ number_format($totalXp) }} XP</span>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="rounded-lg border px-4 py-2 text-sm">Keluar</button></form>
        </div>
    </header>
    <main class="mx-auto max-w-6xl p-6">
        <p class="text-sm text-slate-500">Dashboard / Beranda</p>
        <h1 class="mt-3 text-3xl font-bold">Halo, {{ auth()->user()->name }}</h1>
        @php
            $topics = $course?->modules->flatMap->topics ?? collect();
            $completed = $topics->whereIn('id', $completedTopicIds)->count();
            $percent = $topics->count() ? (int) round($completed / $topics->count() * 100) : 0;
        @endphp
        <section class="mt-8 rounded-2xl bg-slate-900 p-6 text-white">
            <p class="text-sm text-teal-300">Progress keseluruhan</p>
            <p class="mt-2 text-4xl font-bold">{{ $percent }}%</p>
            <p class="mt-2 text-slate-300">{{ $completed }} dari {{ $topics->count() }} topik selesai</p>
        </section>
        <section class="mt-8 grid gap-4 md:grid-cols-4">
            @foreach ($course?->modules ?? [] as $index => $module)
                @php
                    $done = $module->topics->whereIn('id', $completedTopicIds)->count();
                    $complete = $module->isCompletedBy(auth()->user(), $completedTopicIds, $passedQuizIds);
                    $unlocked = $module->isUnlockedFor(auth()->user(), $completedTopicIds, $passedQuizIds);
                    $status = $complete ? 'Selesai' : ($unlocked ? 'Berjalan' : 'Terkunci');
                @endphp
                <article class="rounded-2xl border bg-white p-5 {{ $status === 'Terkunci' ? 'opacity-60' : '' }}">
                    <p class="text-xs font-semibold uppercase text-teal-700">{{ $status }}</p>
                    <h2 class="mt-3 text-lg font-bold">{{ $module->title }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $done }} dari {{ $module->topics->count() }} topik</p>
                    @if ($status !== 'Terkunci')
                        <a class="mt-4 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm text-white" href="{{ route('modules.show', $module) }}">{{ $status === 'Selesai' ? 'Tinjau ulang' : 'Lanjutkan' }}</a>
                    @else
                        <p class="mt-4 text-sm">Selesaikan modul sebelumnya untuk membuka.</p>
                    @endif
                </article>
            @endforeach
        </section>
    </main>
    <nav class="fixed inset-x-0 bottom-0 grid grid-cols-4 border-t bg-white p-2 text-center text-sm md:hidden" aria-label="Navigasi utama">
        <a class="py-2" href="{{ route('dashboard') }}">Dashboard</a><a class="py-2" href="{{ route('sql-lab.index') }}">SQL Lab</a><a class="py-2" href="{{ route('leaderboard.index') }}">Leaderboard</a><a class="py-2" href="{{ route('profile.show') }}">Profil</a>
    </nav>
</body>
</html>
