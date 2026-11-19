<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $topic->title }} | Radix</title>@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))) @vite(['resources/css/app.css', 'resources/js/app.js']) @endif</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto grid max-w-6xl gap-6 p-6 lg:grid-cols-[18rem_1fr]">
        <aside class="rounded-2xl bg-white p-5">
            <a class="text-sm" href="{{ route('modules.show', $topic->module) }}">← Kembali ke Silabus</a>
            <h1 class="mt-4 text-xl font-bold">{{ $topic->module->title }}</h1>
            <nav class="mt-5 space-y-2 text-sm">
                @foreach ($topic->module->topics as $item)
                    <a class="block rounded-lg px-3 py-2 {{ $item->is($topic) ? 'bg-teal-100 font-semibold' : 'hover:bg-slate-100' }}" href="{{ route('topics.show', $item) }}">{{ $item->title }}</a>
                @endforeach
            </nav>
        </aside>
        <article class="rounded-2xl bg-white p-6">
            @if (session('status'))<p class="mb-4 rounded-lg bg-teal-50 p-3 text-sm text-teal-800" role="status">{{ session('status') }}</p>@endif
            <p class="text-sm text-slate-500">Estimasi baca {{ $topic->estimated_minutes }} menit</p>
            <h2 class="mt-2 text-3xl font-bold">{{ $topic->title }}</h2>
            <div class="mt-6 space-y-4">
                @foreach ($topic->content_json['blocks'] ?? [] as $block)
                    @if ($block['type'] === 'paragraph')<p>{{ $block['text'] }}</p>
                    @elseif ($block['type'] === 'tip')<aside class="rounded-xl bg-teal-50 p-4"><h3 class="font-semibold">{{ $block['title'] }}</h3><p class="mt-1 text-sm">{{ $block['text'] }}</p></aside>
                    @endif
                @endforeach
            </div>
            <div class="mt-8 grid gap-4 lg:grid-cols-2">
                <form method="post" action="{{ route('annotations.store', $topic) }}" class="space-y-3 rounded-xl border p-4">
                    @csrf
                    <label class="block text-sm font-medium">Jenis catatan
                        <select name="type" class="mt-1 w-full rounded-lg border p-3">
                            <option value="highlight">Highlight</option>
                            <option value="bookmark">Bookmark</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium">Teks terpilih
                        <textarea name="selected_text" class="mt-1 w-full rounded-lg border p-3" maxlength="5000"></textarea>
                    </label>
                    <label class="block text-sm font-medium">Catatan
                        <textarea name="note" class="mt-1 w-full rounded-lg border p-3" maxlength="1000"></textarea>
                    </label>
                    <button class="rounded-lg border px-4 py-2 text-sm">Simpan catatan</button>
                </form>
                <section class="rounded-xl bg-slate-50 p-4">
                    <h3 class="font-semibold">Catatan saya</h3>
                    @forelse ($annotations as $annotation)
                        <article class="mt-3 rounded-lg bg-white p-3 text-sm">
                            <p class="font-semibold">{{ $annotation->type === 'highlight' ? 'Highlight' : 'Bookmark' }}</p>
                            <p class="mt-1">{{ $annotation->selected_text }}</p>
                            <p class="mt-1 text-slate-600">{{ $annotation->note }}</p>
                            <form method="post" action="{{ route('annotations.destroy', $annotation) }}">@csrf @method('delete')<button class="mt-2 text-red-700">Hapus</button></form>
                        </article>
                    @empty
                        <p class="mt-3 text-sm text-slate-500">Belum ada highlight atau bookmark.</p>
                    @endforelse
                </section>
            </div>
            @if ($topic->understanding)
                <a class="mt-8 inline-block rounded-lg border border-teal-700 px-5 py-3 text-teal-800" href="{{ route('understandings.show', $topic->understanding) }}">Check Your Understanding</a>
            @endif
            <form class="mt-8" method="post" action="{{ route('topics.complete', $topic) }}">@csrf<button class="rounded-lg bg-slate-900 px-5 py-3 text-white">{{ $progress->completed_at ? 'Selesai' : 'Tandai selesai' }}</button></form>
        </article>
    </main>
</body>
</html>
