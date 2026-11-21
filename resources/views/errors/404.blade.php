<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Halaman Tidak Ditemukan | Radix</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="flex min-h-screen items-center justify-center bg-[#f3f7f8] p-6 text-[#102b50]"><main class="max-w-md rounded-2xl bg-white p-8 text-center shadow-xl"><p class="text-sm font-bold uppercase tracking-widest text-teal-600">Radix · 404</p><h1 class="mt-3 text-3xl font-bold">Halaman tidak ditemukan</h1><p class="mt-3 text-slate-600">Halaman yang kamu cari tidak tersedia atau sudah dipindahkan.</p><a class="mt-6 inline-block rounded-xl bg-[#0d294d] px-5 py-3 font-semibold text-white" href="{{ auth()->check() ? route('dashboard') : route('home') }}">Kembali</a></main></body>
</html>
