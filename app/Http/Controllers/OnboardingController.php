<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    private const STEPS = [
        ['title' => 'Peta Modul', 'message' => 'Ini jalur belajarmu, selesaikan berurutan.'],
        ['title' => 'Reader', 'message' => 'Atur font dan tema, lalu highlight bagian penting.'],
        ['title' => 'Lab SQL', 'message' => 'Ketuk Coba, jalankan query, lihat hasilnya.'],
        ['title' => 'Kuis dan Badge', 'message' => 'Jawab kuis, kumpulkan XP dan badge.'],
        ['title' => 'Profil', 'message' => 'Ulangi tur kapan saja dari sini.'],
    ];

    public function show(Request $request): View
    {
        $step = max(1, min((int) $request->integer('step', 1), count(self::STEPS)));

        return view('onboarding.show', ['steps' => self::STEPS, 'step' => $step]);
    }

    public function complete(): RedirectResponse
    {
        auth()->user()->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard');
    }
}
