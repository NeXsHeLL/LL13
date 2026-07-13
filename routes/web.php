<?php

use App\Http\Controllers\LearningCheckpointController;
use App\Http\Controllers\LearningLogController;
use App\Http\Controllers\LearningPathController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [LearningPathController::class, 'index'])->name('dashboard');
    Route::post('learning-paths', [LearningPathController::class, 'store'])->name('learning-paths.store');
    Route::post('learning-paths/quest', [LearningPathController::class, 'storeQuest'])->name('learning-paths.quest.store');
    Route::put('learning-paths/{learningPath}', [LearningPathController::class, 'update'])->name('learning-paths.update');
    Route::delete('learning-paths/{learningPath}', [LearningPathController::class, 'destroy'])->name('learning-paths.destroy');
    Route::post('learning-paths/{learningPath}/logs', [LearningLogController::class, 'store'])->name('learning-paths.logs.store');
    Route::post('learning-paths/{learningPath}/checkpoints', [LearningCheckpointController::class, 'store'])->name('learning-paths.checkpoints.store');
    Route::put('learning-paths/{learningPath}/checkpoints/{learningCheckpoint}', [LearningCheckpointController::class, 'update'])->name('learning-paths.checkpoints.update');
    Route::delete('learning-paths/{learningPath}/checkpoints/{learningCheckpoint}', [LearningCheckpointController::class, 'destroy'])->name('learning-paths.checkpoints.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
