<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LearningLogController extends Controller
{
    public function store(Request $request, LearningPath $learningPath): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('update', $learningPath);

        $validated = $request->validate([
            'learned_on' => 'required|date|before_or_equal:today',
            'minutes' => 'required|integer|min:5|max:720',
            'topic' => 'required|string|max:120',
            'reflection' => 'required|string|max:1000',
        ]);

        $learningPath->logs()->create($validated);

        return back();
    }
}
