<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Content Management | Radix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f7f8] p-4 text-[#102b50] sm:p-6">
<main class="mx-auto max-w-6xl">
    <div class="flex flex-wrap justify-between gap-3"><a class="text-sm font-semibold text-slate-600" href="{{ route('admin.dashboard') }}">← Admin Dashboard</a><a class="text-sm font-semibold text-teal-700" href="{{ route('dashboard') }}">Student Dashboard</a></div>
    <p class="mt-7 text-sm font-semibold uppercase tracking-widest text-teal-700">Administration</p>
    <h1 class="mt-2 text-3xl font-bold">Content Management</h1>
    @if (session('status'))<p class="mt-4 rounded-xl bg-teal-50 p-4" role="status">{{ session('status') }}</p>@endif
    @foreach ($courses as $course)
        <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-bold">{{ $course->title }}</h2>
            @foreach ($course->modules as $module)
                <div class="mt-6 rounded-2xl border border-slate-200 p-5">
                    <h3 class="font-bold">{{ $module->title }}</h3>
                    <div class="mt-4 space-y-4">
                        @foreach ($module->topics as $topic)
                            <article class="rounded-2xl bg-slate-50 p-5">
                                <div class="flex flex-wrap justify-between gap-2"><strong>{{ $topic->title }}</strong><span class="text-xs font-semibold {{ $topic->is_published ? 'text-teal-700' : 'text-slate-500' }}">{{ $topic->is_published ? 'Published' : 'Draft' }}</span></div>
                                <form class="mt-4 grid gap-3" method="post" action="{{ route('admin.content.topics.update', $topic) }}">
                                    @csrf @method('patch')
                                    <input name="title" value="{{ $topic->title }}" class="rounded-xl border border-slate-300 p-3">
                                    <div class="grid gap-3 sm:grid-cols-2"><input name="sort_order" type="number" value="{{ $topic->sort_order }}" min="1" class="rounded-xl border border-slate-300 p-3"><input name="estimated_minutes" type="number" value="{{ $topic->estimated_minutes }}" min="1" class="rounded-xl border border-slate-300 p-3"></div>
                                    <textarea name="content_json" rows="8" class="rounded-xl border border-slate-300 p-3 font-mono text-sm">{{ json_encode($topic->content_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" @checked($topic->is_published) class="h-4 w-4 accent-teal-600"> Published</label>
                                    <button type="submit" class="w-fit rounded-xl bg-[#0d294d] px-4 py-3 font-semibold text-white">Simpan perubahan</button>
                                </form>
                            </article>
                        @endforeach
                    </div>
                    <form class="mt-5 grid gap-3 rounded-2xl bg-teal-50 p-5" method="post" action="{{ route('admin.content.topics.store', $module) }}">
                        @csrf
                        <h4 class="font-bold">Tambah topik</h4>
                        <input name="title" placeholder="Judul topik" required class="rounded-xl border border-slate-300 p-3">
                        <input name="slug" placeholder="slug-topik" required class="rounded-xl border border-slate-300 p-3">
                        <textarea name="content_json" rows="5" required class="rounded-xl border border-slate-300 p-3 font-mono text-sm">{"version":1,"blocks":[{"type":"paragraph","text":"Materi baru"}]}</textarea>
                        <div class="grid gap-3 sm:grid-cols-2"><input name="sort_order" type="number" value="1" min="1" required class="rounded-xl border border-slate-300 p-3"><input name="estimated_minutes" type="number" value="10" min="1" required class="rounded-xl border border-slate-300 p-3"></div>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" class="h-4 w-4 accent-teal-600"> Publish langsung</label>
                        <button type="submit" class="w-fit rounded-xl bg-teal-700 px-4 py-3 font-semibold text-white">Tambah topik</button>
                    </form>
                </div>
            @endforeach
        </section>
    @endforeach
</main>
</body>
</html>
