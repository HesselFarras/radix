<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SQL Lab Management | Radix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f7f8] p-4 text-[#102b50] sm:p-6">
    <main class="mx-auto max-w-6xl">
        <a class="text-sm font-semibold text-slate-600" href="{{ route('admin.dashboard') }}">← Admin Dashboard</a>
        <p class="mt-6 text-sm font-semibold uppercase tracking-widest text-teal-700">Administration</p>
        <h1 class="mt-2 text-3xl font-bold">SQL Lab Management</h1>
        @if (session('status'))
            <p class="mt-4 rounded-xl bg-teal-50 p-4" role="status">{{ session('status') }}</p>
        @endif

        @foreach ($labs as $lab)
            <section class="mt-6 rounded-3xl bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-teal-700">{{ $lab->module->title }}</p>
                        <h2 class="mt-1 text-2xl font-bold">{{ $lab->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Mode: {{ ucfirst($lab->mode) }}</p>
                    </div>
                    <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">{{ $lab->steps->count() }} langkah</span>
                </div>

                <form method="post" action="{{ route('admin.labs.update', $lab) }}" class="mt-4 grid gap-2 rounded-xl bg-slate-50 p-3 sm:grid-cols-2">@csrf @method('patch')<input name="title" value="{{ $lab->title }}" required class="rounded-lg border p-2"><input name="mode" value="{{ $lab->mode }}" required class="rounded-lg border p-2"><textarea name="description" class="rounded-lg border p-2 sm:col-span-2">{{ $lab->description }}</textarea><input name="schema_json" value="{{ json_encode($lab->schema_json) }}" class="rounded-lg border p-2 sm:col-span-2"><input name="sort_order" type="number" min="1" value="{{ $lab->sort_order }}" class="rounded-lg border p-2"><button class="rounded-lg bg-teal-700 px-3 py-2 text-sm font-semibold text-white">Simpan lab</button></form><form method="post" action="{{ route('admin.labs.destroy', $lab) }}" class="mt-3">@csrf @method('delete')<button class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">Hapus lab</button></form>
                <ul class="mt-6 space-y-2">
                    @foreach ($lab->steps as $step)
                        <li class="rounded-xl bg-slate-50 p-4 text-sm"><form method="post" action="{{ route('admin.labs.steps.update', [$lab, $step]) }}" class="grid gap-2 sm:grid-cols-2">@csrf @method('patch')<input name="title" value="{{ $step->title }}" required class="rounded-lg border p-2"><input name="xp_reward" type="number" min="0" value="{{ $step->xp_reward }}" class="rounded-lg border p-2"><textarea name="instruction" required class="rounded-lg border p-2 sm:col-span-2">{{ $step->instruction }}</textarea><textarea name="expected_result_json" class="rounded-lg border p-2">{{ json_encode($step->expected_result_json) }}</textarea><textarea name="reference_query" class="rounded-lg border p-2">{{ $step->reference_query }}</textarea><input name="sort_order" type="number" min="1" value="{{ $step->sort_order }}" class="rounded-lg border p-2"><button class="w-fit rounded-lg bg-teal-700 px-3 py-2 text-xs font-semibold text-white">Simpan langkah</button></form><div class="mt-2 flex items-center justify-between"><span><strong>{{ $loop->iteration }}. {{ $step->title }}</strong></span>
                            <span class="flex items-center gap-2"><span class="text-slate-400">SQL</span><form method="post" action="{{ route('admin.labs.steps.destroy', [$lab, $step]) }}">@csrf @method('delete')<button class="text-xs font-semibold text-red-700">Hapus</button></form></span>
                        </li>
                    @endforeach
                </ul>

                <form class="mt-6 grid gap-3 rounded-2xl bg-teal-50 p-5" method="post" action="{{ route('admin.labs.steps.store', $lab) }}">
                    @csrf
                    <h3 class="font-bold">Tambah langkah</h3>
                    <input name="title" placeholder="Judul langkah" required class="rounded-xl border border-slate-300 p-3">
                    <textarea name="instruction" placeholder="Instruksi" required class="rounded-xl border border-slate-300 p-3"></textarea>
                    <textarea name="expected_result_json" placeholder='{"columns":[],"rows":[]}' class="rounded-xl border border-slate-300 p-3"></textarea>
                    <textarea name="reference_query" placeholder="Query referensi" class="rounded-xl border border-slate-300 p-3"></textarea>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <input name="xp_reward" type="number" value="20" min="0" class="rounded-xl border border-slate-300 p-3">
                        <input name="sort_order" type="number" value="1" min="1" class="rounded-xl border border-slate-300 p-3">
                    </div>
                    <button type="submit" class="w-fit rounded-xl bg-teal-700 px-5 py-3 font-semibold text-white">Tambah langkah</button>
                </form>
            </section>
        @endforeach
    </main>
</body>
</html>
