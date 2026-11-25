<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * @var list<array{key: string, label: string, title: string, message: string, bubble: string}>
     */
    private const STEPS = [
        [
            'key' => 'modules',
            'label' => 'Peta Modul',
            'title' => 'Peta Modul',
            'message' => 'Ini jalur belajarmu. Selesaikan modul secara berurutan, modul berikutnya terbuka setelah modul sebelumnya selesai.',
            'bubble' => 'Halo! Aku Radi, pemandu Radix. Di sini kamu belajar Basis Data dari konsep dasar sampai membuat query SQL sendiri. Yuk kenalan dulu, cuma butuh 1 menit.',
        ],
        [
            'key' => 'reader',
            'label' => 'Reader',
            'title' => 'Module Reader',
            'message' => 'Baca materi per topik, atur ukuran font dan tema, lalu highlight bagian yang penting.',
            'bubble' => 'Tandai kalimat penting dengan highlight supaya gampang diulang nanti.',
        ],
        [
            'key' => 'lab',
            'label' => 'SQL Lab',
            'title' => 'Lab SQL',
            'message' => 'Tulis query, tekan Jalankan, dan lihat hasilnya langsung dalam bentuk tabel.',
            'bubble' => 'Jangan takut salah. Setiap query yang kamu jalankan akan diberi umpan balik.',
        ],
        [
            'key' => 'quiz',
            'label' => 'Kuis & Badge',
            'title' => 'Kuis dan Badge',
            'message' => 'Jawab kuis di akhir modul untuk mengumpulkan XP dan membuka badge.',
            'bubble' => 'Kalau skornya belum lulus, kamu diarahkan kembali ke materi, bukan diblokir.',
        ],
        [
            'key' => 'profile',
            'label' => 'Profil',
            'title' => 'Profil',
            'message' => 'Pantau XP, badge, dan progres sertifikat. Tur ini bisa diulang kapan saja dari Profil atau Pengaturan.',
            'bubble' => 'Selesai! Sekarang kamu siap mulai belajar. Sampai jumpa di modul pertama.',
        ],
    ];

    public function show(Request $request): View
    {
        $step = max(1, min($request->integer('step', 1), count(self::STEPS)));

        $course = Course::query()
            ->where('slug', 'basis-data')
            ->where('is_published', true)
            ->with(['modules' => fn ($query) => $query->where('is_published', true)])
            ->first();

        return view('onboarding.show', [
            'steps' => self::STEPS,
            'step' => $step,
            'currentStep' => self::STEPS[$step - 1],
            'modules' => $course?->modules ?? collect(),
        ]);
    }

    public function complete(): RedirectResponse
    {
        auth()->user()->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard');
    }
}
