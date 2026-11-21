<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuis Management | Radix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f7f8] p-4 text-[#102b50] sm:p-6">
<main class="mx-auto max-w-6xl">
    <a class="text-sm font-semibold text-slate-600" href="{{ route('admin.dashboard') }}">← Admin Dashboard</a>
    <p class="mt-7 text-sm font-semibold uppercase tracking-widest text-teal-700">Administration</p>
    <h1 class="mt-2 text-3xl font-bold">Kuis Management</h1>
    @if (session('status'))<p class="mt-4 rounded-xl bg-teal-50 p-4" role="status">{{ session('status') }}</p>@endif

    @foreach ($quizzes as $quiz)
        <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div><p class="text-sm text-teal-700">{{ $quiz->module->title }}</p><h2 class="mt-1 text-2xl font-bold">{{ $quiz->title }}</h2></div>
                <div class="text-right text-sm text-slate-500"><p>Passing: <strong>{{ $quiz->passing_score }}%</strong></p><p>Max: <strong>{{ $quiz->max_xp }} XP</strong></p></div>
            </div>
            <div class="mt-6 space-y-4">
                @foreach ($quiz->questions as $question)
                    <article class="rounded-2xl border border-slate-200 p-5">
                        <p class="font-bold">{{ $loop->iteration }}. {{ $question->question }} <span class="text-sm font-normal text-slate-500">({{ $question->points }} poin)</span></p>
                        <ul class="mt-3 space-y-2">@foreach ($question->options as $option)<li class="rounded-lg bg-slate-50 p-3 text-sm">{{ $option->option_text }} @if ($option->is_correct)<strong class="text-teal-700">✓ Benar</strong>@endif</li>@endforeach</ul>
                        <form class="mt-4 grid gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-[1fr_auto_auto_auto] sm:items-center" method="post" action="{{ route('admin.questions.options.store', $question) }}">
                            @csrf
                            <input name="option_text" placeholder="Opsi jawaban" required class="rounded-xl border border-slate-300 p-3">
                            <input name="sort_order" type="number" value="1" min="1" class="rounded-xl border border-slate-300 p-3 sm:w-24">
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_correct" value="1" class="h-4 w-4 accent-teal-600"> Benar</label>
                            <button type="submit" class="rounded-xl bg-[#0d294d] px-4 py-3 font-semibold text-white">Tambah opsi</button>
                        </form>
                    </article>
                @endforeach
            </div>
            <form class="mt-6 grid gap-3 rounded-2xl bg-teal-50 p-5" method="post" action="{{ route('admin.quizzes.questions.store', $quiz) }}">
                @csrf
                <h3 class="font-bold">Tambah pertanyaan</h3>
                <input name="question" placeholder="Pertanyaan" required class="rounded-xl border border-slate-300 p-3">
                <textarea name="explanation" placeholder="Pembahasan" class="rounded-xl border border-slate-300 p-3"></textarea>
                <div class="grid gap-3 sm:grid-cols-3"><select name="type" class="rounded-xl border border-slate-300 p-3"><option value="multiple_choice">Pilihan ganda</option><option value="short_answer">Isian</option></select><input name="points" type="number" value="1" min="1" class="rounded-xl border border-slate-300 p-3"><input name="sort_order" type="number" value="1" min="1" class="rounded-xl border border-slate-300 p-3"></div>
                <button type="submit" class="w-fit rounded-xl bg-teal-700 px-4 py-3 font-semibold text-white">Tambah pertanyaan</button>
            </form>
        </section>
    @endforeach
</main>
</body>
</html>
