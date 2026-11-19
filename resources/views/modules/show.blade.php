<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $module->title }} | Radix</title>@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))) @vite(['resources/css/app.css', 'resources/js/app.js']) @endif</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-5xl p-6">
        <a class="text-sm" href="{{ route('dashboard') }}">← Kembali ke Dashboard</a>
        <h1 class="mt-4 text-3xl font-bold">{{ $module->title }}</h1>
        <p class="mt-2 text-slate-600">{{ $module->description }}</p>
        <section class="mt-8 space-y-3">
            @foreach ($module->topics as $topic)
                <article class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div><h2 class="font-semibold">{{ $topic->title }}</h2><p class="text-sm text-slate-500">Estimasi {{ $topic->estimated_minutes }} menit</p></div>
                        <span class="text-sm">{{ $completedTopicIds->contains($topic->id) ? 'Selesai' : 'Belum' }}</span>
                    </div>
                    <a class="mt-3 inline-block rounded-lg bg-slate-900 px-4 py-2 text-sm text-white" href="{{ route('topics.show', $topic) }}">Baca materi</a>
                </article>
            @endforeach
        </section>
        <section class="mt-8 rounded-xl bg-white p-5">
            <h2 class="text-xl font-bold">Kuis modul</h2>
            @forelse ($module->quizzes as $quiz)
                <a class="mt-4 inline-block rounded-lg bg-teal-700 px-5 py-3 text-white" href="{{ route('quizzes.show', $quiz) }}">Mulai {{ $quiz->title }}</a>
            @empty
                <p class="mt-2 text-slate-600">Kuis belum tersedia.</p>
            @endforelse
        </section>
    </main>
</body>
</html>
