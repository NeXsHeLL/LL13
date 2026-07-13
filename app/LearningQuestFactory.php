<?php

namespace App;

use InvalidArgumentException;

class LearningQuestFactory
{
    /**
     * @return list<array<string, mixed>>
     */
    public function catalog(): array
    {
        return collect($this->blueprints())
            ->map(fn (array $blueprint, string $id): array => [
                'id' => $id,
                'title' => $blueprint['path']['title'],
                'level' => $blueprint['level'],
                'description' => $blueprint['description'],
                'source_label' => $blueprint['source_label'],
                'source_url' => $blueprint['source_url'],
                'activity_count' => count($blueprint['checkpoints']),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function ids(): array
    {
        return array_keys($this->blueprints());
    }

    /**
     * @return array{path: array<string, mixed>, checkpoints: list<array<string, mixed>>}
     */
    public function get(string $quest): array
    {
        $blueprint = $this->blueprints()[$quest] ?? null;

        if ($blueprint === null) {
            throw new InvalidArgumentException("Unknown learning quest [{$quest}].");
        }

        return [
            'path' => $blueprint['path'],
            'checkpoints' => $blueprint['checkpoints'],
        ];
    }

    /**
     * @return array{path: array<string, mixed>, checkpoints: list<array<string, mixed>>}
     */
    public function laravelFullStack(): array
    {
        return $this->get('laravel-full-stack');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function blueprints(): array
    {
        return [
            'php-foundations' => [
                'level' => 'beginner',
                'description' => 'Start with PHP syntax, arrays, functions, requests, and small debugging habits before touching framework magic.',
                'source_label' => 'Laracasts Discover: PHP / Languages',
                'source_url' => 'https://laracasts.com/series/discover',
                'path' => [
                    'title' => 'PHP Foundations Quest',
                    'focus_area' => 'PHP basics',
                    'outcome' => 'Read and write practical PHP with variables, arrays, functions, forms, classes, and debugging notes.',
                    'status' => 'active',
                    'weekly_minutes' => 180,
                    'confidence' => 2,
                    'target_date' => now()->addWeeks(4)->toDateString(),
                ],
                'checkpoints' => [
                    $this->mcq('Basics', 'What does a PHP variable always start with?', [
                        'A' => 'A dollar sign',
                        'B' => 'A hash sign',
                        'C' => 'The word var',
                        'D' => 'A table name',
                    ], 'A', 'PHP variables are prefixed with a dollar sign, such as $topic.'),
                    $this->task('Basics', 'Create a values playground', 'Write a small PHP script that prints strings, numbers, booleans, and arrays. Add one comment explaining each type.'),
                    $this->mcq('Basics', 'Which PHP structure is best for a list of related values?', [
                        'A' => 'Array',
                        'B' => 'Route',
                        'C' => 'Migration',
                        'D' => 'Middleware',
                    ], 'A', 'Arrays are PHP data structures for lists and keyed collections.'),
                    $this->task('Intermediate', 'Build a reusable function', 'Create a function that accepts an array of study topics and returns a formatted summary string.'),
                    $this->mcq('Intermediate', 'Why use a class?', [
                        'A' => 'To group related data and behavior',
                        'B' => 'To install Composer packages',
                        'C' => 'To compile CSS',
                        'D' => 'To start Nginx',
                    ], 'A', 'Classes help organize related state and behavior behind a clear interface.'),
                    $this->task('Advanced', 'Debug a small PHP flow', 'Intentionally break a condition or loop, inspect the error, fix it, and write down the exact signal that led to the fix.'),
                ],
            ],
            'laravel-foundations' => [
                'level' => 'beginner',
                'description' => 'Move through the everyday Laravel loop: routes, controllers, views, models, migrations, forms, auth, and tests.',
                'source_label' => 'Laracasts Discover: Laravel From Scratch',
                'source_url' => 'https://laracasts.com/series/discover',
                'path' => [
                    'title' => 'Laravel Foundations Quest',
                    'focus_area' => 'Laravel fundamentals',
                    'outcome' => 'Build a small Laravel feature from route to database with validation, auth awareness, and a feature test.',
                    'status' => 'active',
                    'weekly_minutes' => 240,
                    'confidence' => 2,
                    'target_date' => now()->addWeeks(5)->toDateString(),
                ],
                'checkpoints' => [
                    $this->mcq('Basics', 'What does a Laravel route usually do?', [
                        'A' => 'Maps an HTTP request to application code',
                        'B' => 'Stores passwords in the database',
                        'C' => 'Compiles React components',
                        'D' => 'Creates server users',
                    ], 'A', 'Routes are the entry points for HTTP requests and usually point to closures or controller actions.'),
                    $this->task('Basics', 'Create one route and controller action', 'Add a GET route, point it to a controller action, and return a small page or response.'),
                    $this->mcq('Basics', 'What is a migration for?', [
                        'A' => 'Versioning database schema changes',
                        'B' => 'Styling a page',
                        'C' => 'Sending browser events',
                        'D' => 'Renaming a Git branch',
                    ], 'A', 'Migrations let the database schema evolve in a repeatable, versioned way.'),
                    $this->task('Intermediate', 'Create a model-backed form', 'Create a model, migration, form, validation rules, and save one record through the browser.'),
                    $this->mcq('Intermediate', 'Which layer should reject invalid form input?', [
                        'A' => 'Server-side validation',
                        'B' => 'Only placeholder text',
                        'C' => 'Only CSS classes',
                        'D' => 'Only database auto-increment',
                    ], 'A', 'Client-side hints are helpful, but the server must validate incoming data.'),
                    $this->task('Advanced', 'Prove the feature with a test', 'Write a feature test for the happy path and one validation failure, then run the test by itself.'),
                ],
            ],
            'laravel-full-stack' => [
                'level' => 'intermediate',
                'description' => 'A deeper Laravel 13 path across Eloquent, Inertia, authorization, tests, queues, and shipping discipline.',
                'source_label' => 'Laracasts Discover: Laravel / Frameworks',
                'source_url' => 'https://laracasts.com/series/discover',
                'path' => [
                    'title' => 'Laravel 13 Quest: Basics to Advanced',
                    'focus_area' => 'Laravel full-stack',
                    'outcome' => 'Build confidence across routing, models, validation, Inertia, authorization, testing, queues, and deployment.',
                    'status' => 'active',
                    'weekly_minutes' => 300,
                    'confidence' => 2,
                    'target_date' => now()->addWeeks(6)->toDateString(),
                ],
                'checkpoints' => [
                    $this->mcq('Basics', 'Which Eloquent feature protects against mass-assignment surprises?', [
                        'A' => 'The fillable or guarded model configuration',
                        'B' => 'The Vite config',
                        'C' => 'The queue worker',
                        'D' => 'The mailer transport',
                    ], 'A', 'Mass assignment is controlled on the model through fillable or guarded attributes.'),
                    $this->task('Basics', 'Create a model and migration', 'Generate a small model with a migration, add two useful columns, migrate it, and explain why each column exists.'),
                    $this->mcq('Intermediate', 'What is Inertia mainly helping you avoid?', [
                        'A' => 'Writing a separate JSON API for every server-rendered page',
                        'B' => 'Writing database migrations',
                        'C' => 'Running Composer install',
                        'D' => 'Creating PHP classes',
                    ], 'A', 'Inertia lets Laravel controllers render React pages with props, avoiding a separate SPA API layer for many app screens.'),
                    $this->task('Intermediate', 'Build one validated Inertia form', 'Create a POST route, validate the request, persist a record, and show validation feedback in the Inertia page.'),
                    $this->mcq('Intermediate', 'Where should ownership checks live for model actions?', [
                        'A' => 'Policies or gates',
                        'B' => 'The package lock file',
                        'C' => 'Only the button label',
                        'D' => 'The CSS reset',
                    ], 'A', 'Policies and gates keep authorization explicit and testable on the server.'),
                    $this->task('Intermediate', 'Add ownership authorization', 'Protect a nested resource so only the owner of the parent model can update or delete it.'),
                    $this->mcq('Advanced', 'What should a feature test prove for a protected resource?', [
                        'A' => 'Happy path, validation failure, guest redirect, and unauthorized access',
                        'B' => 'Only that the page has CSS',
                        'C' => 'Only that npm build passes',
                        'D' => 'Only that the database exists',
                    ], 'A', 'Good feature tests cover the behavior users and attackers can trigger through HTTP.'),
                    $this->task('Advanced', 'Ship a production-ready slice', 'Run migrations, feature tests, Pint, frontend build, and verify the app through the browser or HTTP before calling it done.'),
                ],
            ],
            'modern-laravel' => [
                'level' => 'advanced',
                'description' => 'Practice the sharper edges: relationships, queues, real-time events, deploy checks, and framework mental models.',
                'source_label' => 'Laracasts Discover: Advanced Laravel topics',
                'source_url' => 'https://laracasts.com/series/discover',
                'path' => [
                    'title' => 'Modern Laravel Deep Dive Quest',
                    'focus_area' => 'Advanced Laravel',
                    'outcome' => 'Design, test, and explain advanced Laravel features with attention to performance, queues, real-time flows, and deployment.',
                    'status' => 'active',
                    'weekly_minutes' => 360,
                    'confidence' => 2,
                    'target_date' => now()->addWeeks(7)->toDateString(),
                ],
                'checkpoints' => [
                    $this->mcq('Advanced', 'What problem do eager-loaded relationships usually solve?', [
                        'A' => 'N+1 query overhead',
                        'B' => 'Missing CSS variables',
                        'C' => 'A broken Git remote',
                        'D' => 'A disabled submit button',
                    ], 'A', 'Eager loading fetches related models up front to avoid repeated queries inside loops.'),
                    $this->task('Advanced', 'Audit one relationship query', 'Find a relationship displayed in a list, inspect the query count, and add eager loading where it is justified.'),
                    $this->mcq('Advanced', 'Why queue slow work?', [
                        'A' => 'To keep the HTTP request fast and retry work safely',
                        'B' => 'To skip validation',
                        'C' => 'To avoid writing tests',
                        'D' => 'To remove database indexes',
                    ], 'A', 'Queues move slow or retryable work outside the request-response path.'),
                    $this->task('Advanced', 'Create a queued job', 'Move one slow fake task into a queued job, add retry settings, and test that the job is dispatched.'),
                    $this->mcq('Advanced', 'What should a deployment checklist protect?', [
                        'A' => 'Schema, config, assets, workers, and rollback signals',
                        'B' => 'Only the app logo',
                        'C' => 'Only local browser tabs',
                        'D' => 'Only package descriptions',
                    ], 'A', 'A useful deploy check covers the runtime pieces that commonly break production behavior.'),
                    $this->task('Advanced', 'Write a release proof checklist', 'Document the exact commands and HTTP/browser checks needed before you trust a Laravel change in production.'),
                ],
            ],
        ];
    }

    /**
     * @param  array<string, string>  $options
     * @return array<string, mixed>
     */
    private function mcq(string $difficulty, string $title, array $options, string $correctOption, string $explanation): array
    {
        return [
            'title' => $title,
            'notes' => null,
            'activity_type' => 'mcq',
            'difficulty' => strtolower($difficulty),
            'prompt' => $title,
            'options' => collect($options)
                ->map(fn (string $text, string $key): array => ['key' => $key, 'text' => $text])
                ->values()
                ->all(),
            'correct_option' => $correctOption,
            'explanation' => $explanation,
            'is_complete' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function task(string $difficulty, string $title, string $prompt): array
    {
        return [
            'title' => $title,
            'notes' => $prompt,
            'activity_type' => 'task',
            'difficulty' => strtolower($difficulty),
            'prompt' => $prompt,
            'options' => null,
            'correct_option' => null,
            'explanation' => 'Mark this complete after you can explain what changed and why.',
            'is_complete' => false,
        ];
    }
}
