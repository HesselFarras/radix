<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = auth()->user()->setting()->firstOrCreate([]);

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'font_scale' => ['required', 'integer', 'in:80,100,120,150,200'],
            'locale' => ['required', 'in:id'],
            'theme' => ['required', 'in:light'],
            'is_leaderboard_visible' => ['sometimes', 'boolean'],
        ]);

        auth()->user()->update([
            'is_leaderboard_visible' => $request->boolean('is_leaderboard_visible'),
        ]);
        auth()->user()->setting()->updateOrCreate([], [
            'font_scale' => $data['font_scale'],
            'locale' => $data['locale'],
            'theme' => $data['theme'],
        ]);

        return redirect()->route('settings.edit')->with('status', 'Pengaturan disimpan.');
    }
}
