<?php

namespace App\Http\Controllers;

use App\Models\LearningCheckpoint;
use App\Models\LearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LearningCheckpointController extends Controller
{
    public function store(Request $request, LearningPath $learningPath): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('update', $learningPath);

        $validated = $request->validate([
            'title' => 'required|string|max:160',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['position'] = $learningPath->checkpoints()->max('position') + 1;

        $learningPath->checkpoints()->create($validated);

        return back();
    }

    public function update(Request $request, LearningPath $learningPath, LearningCheckpoint $learningCheckpoint): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('update', $learningPath);
        abort_unless($learningCheckpoint->learning_path_id === $learningPath->id, 404);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:160',
            'notes' => 'sometimes|nullable|string|max:500',
            'is_complete' => 'sometimes|required|boolean',
            'user_answer' => 'sometimes|required|string|max:10',
        ]);

        if ($learningCheckpoint->activity_type === 'mcq' && array_key_exists('user_answer', $validated)) {
            $validated['is_complete'] = $validated['user_answer'] === $learningCheckpoint->correct_option;
        }

        $learningCheckpoint->update($validated);

        return back();
    }

    public function destroy(Request $request, LearningPath $learningPath, LearningCheckpoint $learningCheckpoint): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('update', $learningPath);
        abort_unless($learningCheckpoint->learning_path_id === $learningPath->id, 404);

        $learningCheckpoint->delete();

        return back();
    }
}
