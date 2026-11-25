<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Tur Radix · {{ $currentStep['title'] }}</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-[#F7F9FF] p-4 font-sans text-[#0B2345] sm:p-6">
<main class="mx-auto flex min-h-[calc(100vh-2rem)] w-full max-w-[1200px] items-center justify-center py-6">
<section aria-labelledby="tour-title" class="grid w-full max-w-[1200px] overflow-hidden rounded-[20px] border border-[#D5E0E6] bg-white p-0 shadow-[0_2px_4px_rgba(11,35,69,.04),0_12px_32px_rgba(11,35,69,.08)] lg:grid-cols-[45%_55%]">
<div class="relative flex flex-col items-center overflow-hidden bg-[#0B2345] p-6 sm:p-8 lg:min-h-[585px]">
<div aria-hidden="true" class="pointer-events-none absolute left-1/2 top-12 h-[340px] w-[340px] -translate-x-1/2 rounded-full border border-[#19C3A4]/15"><div class="absolute inset-8 rounded-full border border-[#19C3A4]/15"><div class="absolute inset-8 rounded-full border border-[#19C3A4]/20"></div></div></div>
<div class="relative z-10 flex w-full items-center justify-between text-xs"><span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-[#D1FAE5] backdrop-blur"><i aria-hidden="true" class="h-2 w-2 rounded-full bg-[#19C3A4]"></i>Radi · Tur Radix</span><span class="font-mono text-white/70">PANDUAN RADIX</span></div>
<img class="relative z-10 mt-8 h-[180px] w-[200px] object-contain sm:mt-10 sm:h-[260px] sm:w-[280px] lg:h-[314px] lg:w-[321px]" src="{{ asset('images/assets/avatar.png') }}" alt="Avatar Radi, maskot Radix">
<div class="relative z-10 mt-6 w-full rounded-[20px] border border-white/20 bg-white p-5 text-[#0B2345] shadow-[0_4px_16px_rgba(11,35,69,.12)] sm:mt-3"><p aria-live="polite" class="text-[13.5px] leading-[21px]">{{ $currentStep['bubble'] }}</p><div aria-hidden="true" class="absolute left-1/2 -top-2 h-4 w-4 -translate-x-1/2 rotate-45 border-l border-t border-white bg-white"></div></div>
</div>
<div class="flex flex-col justify-between bg-white p-6 sm:p-9 lg:min-h-[585px]">
<div class="w-full">
<div class="flex items-center justify-between gap-3"><span class="inline-flex items-center gap-2 rounded-full border border-[#19C3A4]/20 bg-[#19C3A4]/[.14] px-3 py-1 text-xs font-semibold text-[#103A4C]"><i aria-hidden="true" class="h-1.5 w-1.5 rounded-full bg-[#19C3A4]"></i>Tur Radix · {{ $currentStep['label'] }}</span><span class="text-xs font-semibold text-[#334A5E]">Langkah {{ $step }} dari {{ count($steps) }}</span></div>
<h1 id="tour-title" class="mt-6 text-[28px] font-bold leading-9 tracking-[-.7px]">{{ $currentStep['title'] }}</h1>
<p class="mt-3 max-w-[540px] text-[15px] leading-[23px] text-[#334A5E]">{{ $currentStep['message'] }}</p>

<div class="mt-7 rounded-2xl border border-[#19C3A4]/30 bg-[#EEF3F5]/60 p-4 shadow-[0_0_0_2px_#19C3A4,0_4px_16px_rgba(25,195,164,.15)]" aria-label="Contoh tampilan {{ $currentStep['title'] }}" role="img">
@switch($currentStep['key'])
@case('modules')
<div class="flex items-center justify-between text-[11px]"><strong class="uppercase tracking-[.55px] text-[#103A4C]">Peta modul</strong><span class="rounded bg-[#19C3A4]/20 px-2 py-0.5 font-semibold text-[#103A4C]">{{ $modules->count() }} modul</span></div>
@if($modules->isEmpty())
<p class="mt-4 text-center text-xs text-[#334A5E]">Modul akan tampil di sini setelah materi dipublikasikan.</p>
@else
<div class="relative mt-4 grid gap-2" style="grid-template-columns: repeat({{ min($modules->count(), 4) }}, minmax(0, 1fr))"><div aria-hidden="true" class="absolute left-[12%] right-[12%] top-3 border-t-2 border-dashed border-[#D5E0E6]"></div>
@foreach($modules->take(4) as $module)
@php $status = $loop->first ? 'Terbuka' : 'Terkunci'; @endphp
<div class="relative z-10 flex flex-col items-center gap-1.5"><span class="flex h-7 w-7 items-center justify-center rounded-full {{ $loop->first ? 'bg-[#19C3A4] text-[#0B2345]' : 'border border-[#D5E0E6] bg-white text-[#334A5E]' }} text-[10px] font-bold">{{ $loop->iteration }}</span><small class="text-center text-[10px] font-semibold text-[#334A5E]" title="{{ $module->title }}">{{ Str::limit($module->title, 14) }}</small><span class="rounded px-1.5 py-0.5 text-[9px] font-semibold {{ $loop->first ? 'bg-[#19C3A4]/20 text-[#103A4C]' : 'bg-[#D5E0E6]/60 text-[#334A5E]' }}">{{ $status }}</span></div>
@endforeach
</div>
@endif
@break
@case('reader')
<div class="flex items-center justify-between text-[11px]"><strong class="uppercase tracking-[.55px] text-[#103A4C]">Module Reader</strong><span class="flex gap-1"><span class="rounded border border-[#D5E0E6] bg-white px-2 py-0.5 font-semibold">A−</span><span class="rounded border border-[#D5E0E6] bg-white px-2 py-0.5 font-semibold">A+</span><span class="rounded border border-[#D5E0E6] bg-[#0B2345] px-2 py-0.5 font-semibold text-white">◐</span></span></div>
<div class="mt-4 rounded-xl bg-white p-4 text-[13px] leading-[21px] text-[#334A5E]"><p class="font-semibold text-[#0B2345]">Apa itu Basis Data?</p><p class="mt-2">Basis data adalah kumpulan data yang <mark class="rounded bg-[#69FAD8]/60 px-0.5 text-[#0B2345]">terorganisasi dan saling berhubungan</mark>, disimpan agar mudah diakses dan dikelola.</p></div>
@break
@case('lab')
<div class="flex items-center justify-between text-[11px]"><strong class="uppercase tracking-[.55px] text-[#103A4C]">SQL Lab</strong><span class="rounded bg-[#0B2345] px-2 py-0.5 font-semibold text-white">▶ Jalankan</span></div>
<pre class="mt-4 overflow-x-auto rounded-xl bg-[#0B2345] p-3 font-mono text-[12px] text-[#D1FAE5]">SELECT nama, nim FROM mahasiswa WHERE angkatan = 2024;</pre>
<div class="mt-3 overflow-x-auto rounded-xl bg-white"><table class="w-full text-left text-[12px]"><thead class="text-[#103A4C]"><tr><th class="px-3 py-2">nama</th><th class="px-3 py-2">nim</th></tr></thead><tbody class="text-[#334A5E]"><tr class="border-t border-[#D5E0E6]"><td class="px-3 py-1.5">Dika</td><td class="px-3 py-1.5">2401001</td></tr><tr class="border-t border-[#D5E0E6]"><td class="px-3 py-1.5">Sari</td><td class="px-3 py-1.5">2401002</td></tr></tbody></table></div>
@break
@case('quiz')
<div class="flex items-center justify-between text-[11px]"><strong class="uppercase tracking-[.55px] text-[#103A4C]">Kuis modul</strong><span class="rounded bg-[#19C3A4]/20 px-2 py-0.5 font-semibold text-[#103A4C]">Soal 3/10</span></div>
<div class="mt-4 rounded-xl bg-white p-4 text-[13px]"><p class="font-semibold">Perintah SQL untuk mengambil data adalah…</p><div class="mt-3 grid gap-2"><span class="rounded-lg border border-[#D5E0E6] px-3 py-2 text-[#334A5E]">A. INSERT</span><span class="rounded-lg border-2 border-[#19C3A4] bg-[#19C3A4]/10 px-3 py-2 font-semibold">B. SELECT ✓</span></div></div>
<div class="mt-3 flex items-center gap-2 text-[12px] font-semibold text-[#103A4C]"><span class="rounded-full bg-[#0B2345] px-3 py-1 text-white">+50 XP</span><span class="rounded-full border border-[#19C3A4] bg-white px-3 py-1">🏅 Badge baru: Penjelajah Query</span></div>
@break
@case('profile')
<div class="flex items-center justify-between text-[11px]"><strong class="uppercase tracking-[.55px] text-[#103A4C]">Profil</strong><span class="rounded border border-[#D5E0E6] bg-white px-2 py-0.5 font-semibold">↻ Ulangi tur</span></div>
<div class="mt-4 grid grid-cols-3 gap-2 text-center"><div class="rounded-xl bg-white p-3"><p class="text-lg font-bold">1.250</p><p class="text-[11px] text-[#334A5E]">Total XP</p></div><div class="rounded-xl bg-white p-3"><p class="text-lg font-bold">4</p><p class="text-[11px] text-[#334A5E]">Badge</p></div><div class="rounded-xl bg-white p-3"><p class="text-lg font-bold">50%</p><p class="text-[11px] text-[#334A5E]">Sertifikat</p></div></div>
@break
@endswitch
</div>
</div>

<div class="mt-8">
<nav aria-label="Langkah tur" class="border-t border-[#D5E0E6]/60 pt-4"><ol class="grid grid-cols-5 gap-2 text-center text-[11px] text-[#334A5E]">
@foreach($steps as $item)
<li><a href="{{ route('onboarding.show', ['step' => $loop->iteration]) }}" @if($loop->iteration === $step) aria-current="step" @endif class="flex min-h-11 flex-col items-center justify-center rounded-lg px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#19C3A4] {{ $loop->iteration === $step ? 'font-bold text-[#0B2345]' : ($loop->iteration < $step ? 'text-[#103A4C]' : '') }}"><span aria-hidden="true">{{ $loop->iteration === $step ? '●' : ($loop->iteration < $step ? '✓' : '○') }}</span><small class="mt-1 block">{{ $item['label'] }}</small></a></li>
@endforeach
</ol></nav>
<form id="complete-tour" class="hidden" method="post" action="{{ route('onboarding.complete') }}">@csrf</form>
<div class="mt-6 flex items-center justify-between gap-3">
@if($step < count($steps))
<button type="submit" form="complete-tour" class="min-h-11 rounded-xl px-4 py-3 text-sm font-bold text-[#334A5E] underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#19C3A4]">Lewati tur</button>
@else
<span></span>
@endif
<div class="flex items-center gap-3">
@if($step > 1)
<a class="min-h-11 rounded-xl border border-[#D5E0E6] px-5 py-3 text-sm font-bold text-[#0B2345] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#19C3A4]" href="{{ route('onboarding.show', ['step' => $step - 1]) }}" rel="prev">← Kembali</a>
@endif
@if($step < count($steps))
<a class="min-h-11 rounded-xl bg-[#69FAD8] px-7 py-3 text-sm font-bold text-[#0B2345] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0B2345]" href="{{ route('onboarding.show', ['step' => $step + 1]) }}" rel="next">Lanjut →</a>
@else
<button type="submit" form="complete-tour" class="min-h-11 rounded-xl bg-[#69FAD8] px-7 py-3 text-sm font-bold text-[#0B2345] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0B2345]">Mulai belajar →</button>
@endif
</div>
</div>
<p class="mt-4 text-center text-xs text-[#334A5E]"><span aria-hidden="true" class="mr-1">◷</span>Tur singkat ini bisa diulang kapan saja dari Profil atau Pengaturan.</p>
</div>
</div>
</section>
</main>
</body></html>
