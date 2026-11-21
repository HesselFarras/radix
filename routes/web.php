<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BadgeManagementController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ErdPuzzleManagementController;
use App\Http\Controllers\Admin\LabController;
use App\Http\Controllers\Admin\QuizManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ErdPuzzleController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SqlLabController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UnderstandingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/manifest.webmanifest', function () {
    abort_unless(is_file(public_path('manifest.webmanifest')), 404);

    return response(file_get_contents(public_path('manifest.webmanifest')), 200, ['Content-Type' => 'application/manifest+json']);
});
Route::get('/service-worker.js', function () {
    abort_unless(is_file(public_path('service-worker.js')), 404);

    return response(file_get_contents(public_path('service-worker.js')), 200, ['Content-Type' => 'application/javascript']);
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::get('/profile', ProfileController::class)->name('profile.show');
    Route::get('/certificate', [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/leaderboard', LeaderboardController::class)->name('leaderboard.index');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');
    Route::get('/sql-lab', [SqlLabController::class, 'index'])->name('sql-lab.index');
    Route::get('/sql-lab/{lab}', [SqlLabController::class, 'show'])->name('sql-lab.show');
    Route::post('/sql-lab/{lab}/steps/{step}/attempts', [SqlLabController::class, 'saveDraft'])->middleware('throttle:learning-submit')->name('sql-lab.attempts.store');
    Route::get('/erd-puzzle', [ErdPuzzleController::class, 'index'])->name('erd-puzzle.index');
    Route::get('/erd-puzzle/{puzzle}', [ErdPuzzleController::class, 'show'])->name('erd-puzzle.show');
    Route::post('/erd-puzzle/{puzzle}/attempts', [ErdPuzzleController::class, 'submit'])->middleware('throttle:learning-submit')->name('erd-puzzle.attempts.store');
    Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
    Route::post('/topics/{topic}/complete', [TopicController::class, 'complete'])->name('topics.complete');
    Route::post('/topics/{topic}/annotations', [TopicController::class, 'annotate'])->name('annotations.store');
    Route::delete('/annotations/{annotation}', [TopicController::class, 'deleteAnnotation'])->name('annotations.destroy');
    Route::get('/understandings/{understanding}', [UnderstandingController::class, 'show'])->name('understandings.show');
    Route::post('/understandings/{understanding}', [UnderstandingController::class, 'submit'])->name('understandings.submit');
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}', [QuizController::class, 'submit'])->middleware('throttle:learning-submit')->name('quizzes.submit');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::get('/content', [ContentController::class, 'index'])->name('content.index');
    Route::post('/modules/{module}/topics', [ContentController::class, 'storeTopic'])->name('content.topics.store');
    Route::patch('/topics/{topic}', [ContentController::class, 'updateTopic'])->name('content.topics.update');
    Route::delete('/topics/{topic}', [ContentController::class, 'destroyTopic'])->name('content.topics.destroy');
    Route::get('/labs', [LabController::class, 'index'])->name('labs.index');
    Route::post('/labs', [LabController::class, 'store'])->name('labs.store');
    Route::post('/labs/{lab}/steps', [LabController::class, 'storeStep'])->name('labs.steps.store');
    Route::patch('/labs/{lab}', [LabController::class, 'update'])->name('labs.update');
    Route::patch('/labs/{lab}/steps/{step}', [LabController::class, 'updateStep'])->name('labs.steps.update');
    Route::delete('/labs/{lab}', [LabController::class, 'destroy'])->name('labs.destroy');
    Route::delete('/labs/{lab}/steps/{step}', [LabController::class, 'destroyStep'])->name('labs.steps.destroy');
    Route::get('/quizzes', [QuizManagementController::class, 'index'])->name('quizzes.index');
    Route::post('/quizzes', [QuizManagementController::class, 'store'])->name('quizzes.store');
    Route::post('/quizzes/{quiz}/questions', [QuizManagementController::class, 'storeQuestion'])->name('quizzes.questions.store');
    Route::post('/questions/{question}/options', [QuizManagementController::class, 'storeOption'])->name('questions.options.store');
    Route::get('/badges', [BadgeManagementController::class, 'index'])->name('badges.index');
    Route::post('/badges', [BadgeManagementController::class, 'store'])->name('badges.store');
    Route::patch('/badges/{badge}', [BadgeManagementController::class, 'update'])->name('badges.update');
    Route::delete('/badges/{badge}', [BadgeManagementController::class, 'destroy'])->name('badges.destroy');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/active', [UserManagementController::class, 'toggleActive'])->name('users.active');
    Route::patch('/users/{user}/leaderboard', [UserManagementController::class, 'toggleLeaderboard'])->name('users.leaderboard');
    Route::delete('/users/{user}/modules/{module}/progress', [UserManagementController::class, 'resetModuleProgress'])->name('users.progress.reset');
    Route::get('/audit-logs', AuditLogController::class)->name('audit-logs.index');
    Route::get('/erd-puzzles', [ErdPuzzleManagementController::class, 'index'])->name('erd-puzzles.index');
    Route::post('/erd-puzzles', [ErdPuzzleManagementController::class, 'store'])->name('erd-puzzles.store');
    Route::patch('/erd-puzzles/{puzzle}', [ErdPuzzleManagementController::class, 'update'])->name('erd-puzzles.update');
    Route::patch('/erd-puzzles/{puzzle}/components/{component}', [ErdPuzzleManagementController::class, 'updateComponent'])->name('erd-puzzles.components.update');
    Route::patch('/erd-puzzles/{puzzle}/relations/{relation}', [ErdPuzzleManagementController::class, 'updateRelation'])->name('erd-puzzles.relations.update');
    Route::post('/erd-puzzles/{puzzle}/components', [ErdPuzzleManagementController::class, 'storeComponent'])->name('erd-puzzles.components.store');
    Route::post('/erd-puzzles/{puzzle}/relations', [ErdPuzzleManagementController::class, 'storeRelation'])->name('erd-puzzles.relations.store');
    Route::delete('/erd-puzzles/{puzzle}/components/{component}', [ErdPuzzleManagementController::class, 'destroyComponent'])->name('erd-puzzles.components.destroy');
    Route::delete('/erd-puzzles/{puzzle}/relations/{relation}', [ErdPuzzleManagementController::class, 'destroyRelation'])->name('erd-puzzles.relations.destroy');
});
