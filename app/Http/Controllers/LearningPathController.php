<?php

namespace App\Http\Controllers;

use App\LearningQuestFactory;
use App\Models\LearningPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LearningPathController extends Controller
{
    public function index(Request $request, LearningQuestFactory $learningQuestFactory): Response
    {
        $user = $request->user();

        Gate::forUser($user)->authorize('viewAny', LearningPath::class);

        $paths = $user->learningPaths()
            ->withSum('logs as logged_minutes', 'minutes')
            ->withCount([
                'checkpoints',
                'checkpoints as completed_checkpoints_count' => fn ($query) => $query->where('is_complete', true),
            ])
            ->with([
                'logs' => fn ($query) => $query
                    ->latest('learned_on')
                    ->latest()
                    ->limit(3),
                'checkpoints' => fn ($query) => $query
                    ->orderBy('position')
                    ->oldest(),
            ])
            ->latest()
            ->get()
            ->map(fn (LearningPath $path): array => [
                'id' => $path->id,
                'title' => $path->title,
                'focus_area' => $path->focus_area,
                'outcome' => $path->outcome,
                'status' => $path->status,
                'weekly_minutes' => $path->weekly_minutes,
                'confidence' => $path->confidence,
                'target_date' => $path->target_date?->toDateString(),
                'logged_minutes' => (int) ($path->logged_minutes ?? 0),
                'checkpoints_count' => $path->checkpoints_count,
                'completed_checkpoints_count' => $path->completed_checkpoints_count,
                'created_at' => $path->created_at->toDateString(),
                'checkpoints' => $path->checkpoints->map(fn ($checkpoint): array => [
                    'id' => $checkpoint->id,
                    'title' => $checkpoint->title,
                    'notes' => $checkpoint->notes,
                    'activity_type' => $checkpoint->activity_type,
                    'difficulty' => $checkpoint->difficulty,
                    'prompt' => $checkpoint->prompt,
                    'options' => $checkpoint->options,
                    'user_answer' => $checkpoint->user_answer,
                    'explanation' => $checkpoint->explanation,
                    'is_complete' => $checkpoint->is_complete,
                ]),
                'logs' => $path->logs->map(fn ($log): array => [
                    'id' => $log->id,
                    'learned_on' => $log->learned_on->toDateString(),
                    'minutes' => $log->minutes,
                    'topic' => $log->topic,
                    'reflection' => $log->reflection,
                ]),
            ]);

        $stats = [
            'active_paths' => $paths->where('status', 'active')->count(),
            'total_minutes' => $paths->sum('logged_minutes'),
            'planned_minutes' => $paths->whereIn('status', ['planned', 'active'])->sum('weekly_minutes'),
            'average_confidence' => round($paths->avg('confidence') ?? 0, 1),
            'completed_checkpoints' => $paths->sum('completed_checkpoints_count'),
        ];

        return Inertia::render('dashboard', [
            'paths' => $paths,
            'stats' => $stats,
            'questCatalog' => $learningQuestFactory->catalog(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('create', LearningPath::class);

        $validated = $request->validate($this->rules());

        $request->user()->learningPaths()->create($validated);

        return back();
    }

    public function storeQuest(Request $request, LearningQuestFactory $learningQuestFactory): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('create', LearningPath::class);

        $validated = $request->validate([
            'quest' => ['required', 'string', Rule::in($learningQuestFactory->ids())],
        ]);

        $blueprint = $learningQuestFactory->get($validated['quest']);
        $path = $request->user()->learningPaths()->create($blueprint['path']);

        foreach ($blueprint['checkpoints'] as $index => $checkpoint) {
            $path->checkpoints()->create([
                ...$checkpoint,
                'position' => $index + 1,
            ]);
        }

        return back();
    }

    public function update(Request $request, LearningPath $learningPath): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('update', $learningPath);

        $learningPath->update($request->validate($this->rules()));

        return back();
    }

    public function destroy(Request $request, LearningPath $learningPath): RedirectResponse
    {
        Gate::forUser($request->user())->authorize('delete', $learningPath);

        $learningPath->delete();

        return back();
    }

    /**
     * @return array<string, string>
     */
    private function rules(): array
    {
        return [
            'title' => 'required|string|max:120',
            'focus_area' => 'required|string|max:80',
            'outcome' => 'required|string|max:500',
            'status' => 'required|string|in:planned,active,paused,completed',
            'weekly_minutes' => 'required|integer|min:15|max:3000',
            'confidence' => 'required|integer|min:1|max:5',
            'target_date' => 'nullable|date|after_or_equal:today',
        ];
    }
}
