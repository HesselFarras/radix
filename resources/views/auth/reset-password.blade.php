<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Kata Sandi | Radix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-6 text-slate-900">
    <main class="mx-auto mt-12 max-w-md rounded-2xl bg-white p-8 shadow">
        <p class="text-sm font-semibold uppercase tracking-widest text-teal-600">Radix</p>
        <h1 class="mt-3 text-3xl font-bold">Buat Kata Sandi Baru</h1>
        @if ($errors->any())<div class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-700" role="alert">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label class="block"><span class="font-medium">Email</span><input name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email" class="mt-2 w-full rounded-lg border p-3"></label>
            <label class="block"><span class="font-medium">Kata Sandi Baru</span><input name="password" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-lg border p-3"></label>
            <label class="block"><span class="font-medium">Konfirmasi Kata Sandi</span><input name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-lg border p-3"></label>
            <button class="w-full rounded-lg bg-slate-900 p-3 font-semibold text-white">Simpan Kata Sandi</button>
        </form>
    </main>
</body>
</html>
