<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizManagementController extends Controller
{
    public function index(): View
    {
        $quizzes = Quiz::with(['module', 'questions.options'])->latest()->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'module_id' => ['required', 'exists:modules,id'], 'title' => ['required', 'string', 'max:255'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'], 'max_xp' => ['required', 'integer', 'min:0'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        Quiz::create($data);

        return back()->with('status', 'Kuis dibuat.');
    }

    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:multiple_choice,short_answer'], 'question' => ['required', 'string'],
            'explanation' => ['nullable', 'string'], 'points' => ['required', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);
        $data['quiz_id'] = $quiz->id;
        Question::create($data);

        return back()->with('status', 'Soal dibuat.');
    }

    public function storeOption(Request $request, Question $question): RedirectResponse
    {
        $data = $request->validate(['option_text' => ['required', 'string'], 'sort_order' => ['required', 'integer', 'min:1']]);
        $data['question_id'] = $question->id;
        $data['is_correct'] = $request->boolean('is_correct');
        QuestionOption::create($data);

        return back()->with('status', 'Opsi dibuat.');
    }
}
