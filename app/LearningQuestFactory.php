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
            ->map(function (array $blueprint, string $id): array {
                $checkpoints = collect($this->checkpointsFor($id, $blueprint));

                return [
                    'id' => $id,
                    'title' => $blueprint['path']['title'],
                    'level' => $blueprint['level'],
                    'description' => $blueprint['description'],
                    'source_label' => $blueprint['source_label'],
                    'source_url' => $blueprint['source_url'],
                    'activity_count' => $checkpoints->count(),
                    'mcq_count' => $checkpoints->where('activity_type', 'mcq')->count(),
                    'task_count' => $checkpoints->where('activity_type', 'task')->count(),
                    'phase_counts' => $checkpoints
                        ->countBy('difficulty')
                        ->only(['basics', 'intermediate', 'advanced'])
                        ->all(),
                ];
            })
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
            'checkpoints' => $this->checkpointsFor($quest, $blueprint),
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
                    $this->mcq('Basics', 'Which operator joins two strings in PHP?', [
                        'A' => 'The dot operator',
                        'B' => 'The plus operator',
                        'C' => 'The arrow operator',
                        'D' => 'The spread operator',
                    ], 'A', 'PHP uses the dot operator to concatenate strings.'),
                    $this->task('Basics', 'Format a study summary string', 'Create variables for a topic, minutes studied, and confidence score, then print a readable sentence using string concatenation or interpolation.'),
                    $this->mcq('Basics', 'What does an associative array use?', [
                        'A' => 'Named keys',
                        'B' => 'Only numeric indexes',
                        'C' => 'Only database IDs',
                        'D' => 'Only class names',
                    ], 'A', 'Associative arrays store values behind named keys such as topic or minutes.'),
                    $this->task('Intermediate', 'Transform an array of topics', 'Start with an array of topics, filter out completed ones, map the rest into labels, and print the final list.'),
                    $this->mcq('Intermediate', 'What does type declaration help PHP catch?', [
                        'A' => 'Unexpected argument or return value types',
                        'B' => 'Missing CSS classes',
                        'C' => 'Expired sessions',
                        'D' => 'Uncommitted Git files',
                    ], 'A', 'Type declarations make function contracts clearer and help PHP catch incompatible values.'),
                    $this->task('Intermediate', 'Add typed function boundaries', 'Write two typed functions: one that calculates total minutes and one that returns whether a topic needs review.'),
                    $this->mcq('Advanced', 'What is the main value of dependency injection?', [
                        'A' => 'Passing collaborators instead of hard-coding them',
                        'B' => 'Avoiding all PHP classes',
                        'C' => 'Skipping Composer installs',
                        'D' => 'Changing the database engine',
                    ], 'A', 'Dependency injection keeps code easier to test and change by receiving collaborators from the outside.'),
                    $this->task('Advanced', 'Refactor procedural code into a class', 'Move a small procedural script into a class with a constructor, one public method, and one private helper.'),
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
                    $this->mcq('Basics', 'Where do web routes live in a standard Laravel app?', [
                        'A' => 'routes/web.php',
                        'B' => 'package.json',
                        'C' => 'public/favicon.ico',
                        'D' => 'storage/logs',
                    ], 'A', 'Browser-facing web routes are normally registered in routes/web.php.'),
                    $this->task('Basics', 'Name one route', 'Add a named route and use that name when generating a URL from a view or Inertia page.'),
                    $this->mcq('Intermediate', 'What does route model binding usually remove?', [
                        'A' => 'Manual find-or-fail lookup code',
                        'B' => 'The need for database migrations',
                        'C' => 'Every authorization check',
                        'D' => 'All frontend JavaScript',
                    ], 'A', 'Route model binding lets Laravel resolve model parameters for controller actions.'),
                    $this->task('Intermediate', 'Use route model binding', 'Create a show or update route that receives a model instance through route model binding, then test the 404 behavior.'),
                    $this->mcq('Intermediate', 'Why use Eloquent relationships?', [
                        'A' => 'To express how models connect',
                        'B' => 'To replace Composer',
                        'C' => 'To compile Tailwind classes',
                        'D' => 'To hide route names',
                    ], 'A', 'Relationships make model connections explicit and easier to query.'),
                    $this->task('Intermediate', 'Add one relationship pair', 'Create a belongsTo and hasMany pair, then render related records on a page.'),
                    $this->mcq('Advanced', 'What should happen when an unauthenticated user hits a protected route?', [
                        'A' => 'They should be redirected or rejected by middleware',
                        'B' => 'The database should be dropped',
                        'C' => 'The page should silently save data',
                        'D' => 'The queue worker should restart',
                    ], 'A', 'Authentication middleware prevents guests from using protected application areas.'),
                    $this->task('Advanced', 'Protect a route group', 'Move related routes behind auth middleware and add a test proving guests cannot access them.'),
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
                    $this->mcq('Intermediate', 'What does withCount help you avoid?', [
                        'A' => 'Counting related records manually in loops',
                        'B' => 'Writing route names',
                        'C' => 'Installing frontend dependencies',
                        'D' => 'Creating validation messages',
                    ], 'A', 'withCount adds related counts to query results without loading every related model.'),
                    $this->task('Intermediate', 'Add one aggregate to a dashboard', 'Use withCount or withSum to show a useful metric on an index page without doing the calculation in React.'),
                    $this->mcq('Intermediate', 'Why keep validation on the server even with React forms?', [
                        'A' => 'The server is the trust boundary',
                        'B' => 'React cannot display errors',
                        'C' => 'Browsers always submit valid data',
                        'D' => 'Databases never reject bad data',
                    ], 'A', 'Frontend validation improves UX, but server-side validation protects the application boundary.'),
                    $this->task('Advanced', 'Add a policy-backed destructive action', 'Create a delete action, authorize it with a policy, and test owner and non-owner behavior.'),
                    $this->mcq('Advanced', 'What does a queued listener help with?', [
                        'A' => 'Handling slow follow-up work outside the request',
                        'B' => 'Replacing route middleware',
                        'C' => 'Making all tests unnecessary',
                        'D' => 'Removing database transactions',
                    ], 'A', 'Queued listeners are useful when an event should trigger slow or retryable work after the response.'),
                    $this->task('Advanced', 'Create one event-driven workflow', 'Dispatch an event after a model is created, listen for it, and assert the listener or job is triggered in a test.'),
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
                    $this->mcq('Advanced', 'When should you add an index?', [
                        'A' => 'When a column is commonly filtered, sorted, or joined',
                        'B' => 'Whenever a button is added',
                        'C' => 'Only after deleting migrations',
                        'D' => 'Only for CSS class names',
                    ], 'A', 'Indexes help the database find rows efficiently for common filters, sorts, and joins.'),
                    $this->task('Advanced', 'Review one migration for indexes', 'Pick a migration, identify the likely query patterns, and add or justify indexes for those patterns.'),
                    $this->mcq('Advanced', 'Why avoid lazy-loading relationships in a loop?', [
                        'A' => 'It can create an N+1 query pattern',
                        'B' => 'It prevents CSS from loading',
                        'C' => 'It disables migrations',
                        'D' => 'It removes validation rules',
                    ], 'A', 'Lazy-loading a relationship per row can create one query for the list plus one query for each item.'),
                    $this->task('Advanced', 'Write an N+1 regression test', 'Add a small test or query-count check around a list page so relationship loading does not quietly regress.'),
                    $this->mcq('Advanced', 'What belongs in a Form Request?', [
                        'A' => 'Authorization and validation for one request shape',
                        'B' => 'Every database query in the app',
                        'C' => 'Compiled JavaScript assets',
                        'D' => 'GitHub remote configuration',
                    ], 'A', 'Form Requests centralize authorization and validation for a specific incoming request.'),
                    $this->task('Advanced', 'Extract controller validation', 'Move inline validation from one controller action into a Form Request and keep the feature tests passing.'),
                    $this->mcq('Advanced', 'What is the safest way to handle secrets?', [
                        'A' => 'Read them from environment-backed configuration',
                        'B' => 'Commit them into README examples',
                        'C' => 'Place them in frontend props',
                        'D' => 'Store them in CSS variables',
                    ], 'A', 'Secrets should stay out of source control and be accessed through configuration backed by environment values.'),
                    $this->task('Advanced', 'Audit one config boundary', 'Find one environment-driven setting, verify it is documented in .env.example, and confirm no secret value is committed.'),
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function checkpointsFor(string $quest, array $blueprint): array
    {
        return [
            ...$blueprint['checkpoints'],
            ...($this->practiceBatch()[$quest] ?? []),
        ];
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    private function practiceBatch(): array
    {
        return [
            'php-foundations' => [
                $this->mcq('Basics', 'Which PHP superglobal contains query string values?', [
                    'A' => '$_GET',
                    'B' => '$_POST',
                    'C' => '$_SERVER',
                    'D' => '$_SESSION',
                ], 'A', '$_GET contains values sent in the URL query string, such as ?topic=routes.'),
                $this->mcq('Basics', 'What does foreach help you do?', [
                    'A' => 'Loop over arrays and iterable values',
                    'B' => 'Create a database table',
                    'C' => 'Start a queue worker',
                    'D' => 'Import CSS',
                ], 'A', 'foreach is the common PHP loop for walking through each item in an array or iterable.'),
                $this->mcq('Basics', 'What does isset check?', [
                    'A' => 'Whether a variable or key exists and is not null',
                    'B' => 'Whether a route has middleware',
                    'C' => 'Whether a migration was rolled back',
                    'D' => 'Whether a class has CSS',
                ], 'A', 'isset is useful before reading optional values because it checks that the value exists and is not null.'),
                $this->task('Basics', 'Build a request data sandbox', 'Create a tiny PHP page that reads one query string value, validates it exists, and prints a safe fallback when it does not.'),
                $this->mcq('Intermediate', 'Why prefer small functions?', [
                    'A' => 'They make behavior easier to name, test, and reuse',
                    'B' => 'They remove the need for variables',
                    'C' => 'They automatically create database rows',
                    'D' => 'They compile JavaScript faster',
                ], 'A', 'Small functions create clear boundaries and make it easier to reason about one behavior at a time.'),
                $this->mcq('Intermediate', 'What does a namespace prevent?', [
                    'A' => 'Class name collisions across code areas',
                    'B' => 'All runtime exceptions',
                    'C' => 'Missing database indexes',
                    'D' => 'HTML escaping issues',
                ], 'A', 'Namespaces let different parts of an app use clear class names without colliding globally.'),
                $this->mcq('Intermediate', 'What is Composer mainly used for?', [
                    'A' => 'Managing PHP dependencies and autoloading',
                    'B' => 'Managing browser cookies',
                    'C' => 'Writing SQL indexes automatically',
                    'D' => 'Serving Vite assets',
                ], 'A', 'Composer installs PHP packages and generates the autoloader Laravel relies on.'),
                $this->task('Intermediate', 'Create a tiny value object', 'Make a small class that represents a study session with topic and minutes, then add one method that formats the session.'),
                $this->mcq('Advanced', 'Why catch a specific exception type?', [
                    'A' => 'To handle one known failure without hiding unrelated bugs',
                    'B' => 'To disable PHP errors globally',
                    'C' => 'To skip validation forever',
                    'D' => 'To make arrays immutable',
                ], 'A', 'Specific catches keep error handling intentional and avoid swallowing failures you did not expect.'),
                $this->mcq('Advanced', 'What does an interface describe?', [
                    'A' => 'A contract a class promises to implement',
                    'B' => 'A database backup',
                    'C' => 'A browser route',
                    'D' => 'A Tailwind utility',
                ], 'A', 'Interfaces describe required methods so code can depend on a contract instead of a concrete class.'),
                $this->mcq('Advanced', 'Why avoid hidden global state?', [
                    'A' => 'It makes behavior harder to test and predict',
                    'B' => 'It prevents string concatenation',
                    'C' => 'It blocks all class loading',
                    'D' => 'It removes PHP arrays',
                ], 'A', 'Hidden global state creates invisible dependencies, which makes debugging and tests less reliable.'),
                $this->task('Advanced', 'Refactor toward explicit inputs', 'Find a function that reads global state, pass the needed value as an argument, and note how the function became easier to test.'),
            ],
            'laravel-foundations' => [
                $this->mcq('Basics', 'What does php artisan route:list show?', [
                    'A' => 'Registered routes, methods, names, and actions',
                    'B' => 'Only database rows',
                    'C' => 'Only frontend dependencies',
                    'D' => 'Only queued jobs',
                ], 'A', 'route:list is a fast way to inspect the routes Laravel has registered.'),
                $this->mcq('Basics', 'What does a controller action usually return?', [
                    'A' => 'A response, redirect, view, or Inertia render',
                    'B' => 'Only a migration file',
                    'C' => 'Only a Composer package',
                    'D' => 'Only a shell prompt',
                ], 'A', 'Controller actions handle a request and return a response Laravel can send to the browser.'),
                $this->mcq('Basics', 'What does the config helper read?', [
                    'A' => 'Values from Laravel configuration files',
                    'B' => 'Only request body values',
                    'C' => 'Only compiled CSS',
                    'D' => 'Only browser local storage',
                ], 'A', 'config() reads values from files under config, which can be backed by environment variables.'),
                $this->task('Basics', 'Trace one request by hand', 'Pick one route and write down the route, controller method, validation, model call, and response in order.'),
                $this->mcq('Intermediate', 'Why use a Form Request?', [
                    'A' => 'To move validation and authorization for one request into a dedicated class',
                    'B' => 'To replace all controllers',
                    'C' => 'To skip database constraints',
                    'D' => 'To remove route names',
                ], 'A', 'Form Requests keep controller actions smaller and make request rules easier to reuse and test.'),
                $this->mcq('Intermediate', 'What does old input support after validation fails?', [
                    'A' => 'Repopulating form fields',
                    'B' => 'Compiling assets',
                    'C' => 'Creating queue tables',
                    'D' => 'Changing the app key',
                ], 'A', 'Laravel flashes old input so forms can show what the user submitted after a validation redirect.'),
                $this->mcq('Intermediate', 'What does a policy answer?', [
                    'A' => 'Whether a user may perform an action on a model',
                    'B' => 'Which CSS file is largest',
                    'C' => 'Which package manager is installed',
                    'D' => 'Whether Vite is running',
                ], 'A', 'Policies put model-specific authorization decisions in one clear class.'),
                $this->task('Intermediate', 'Add a Form Request', 'Extract validation for one create or update action into a Form Request and confirm the same validation errors still appear.'),
                $this->mcq('Advanced', 'Why test a validation failure?', [
                    'A' => 'It proves bad input is rejected and the user gets useful feedback',
                    'B' => 'It proves CSS is minified',
                    'C' => 'It proves every route is public',
                    'D' => 'It proves Composer can update',
                ], 'A', 'Validation failure tests protect the server boundary and the user-facing error path.'),
                $this->mcq('Advanced', 'What does RefreshDatabase give feature tests?', [
                    'A' => 'A clean database state around tests',
                    'B' => 'A faster GPU',
                    'C' => 'Automatic frontend screenshots',
                    'D' => 'A public server URL',
                ], 'A', 'RefreshDatabase keeps tests isolated by managing database state between test runs.'),
                $this->mcq('Advanced', 'Why keep authorization checks server-side?', [
                    'A' => 'Frontend controls can be bypassed',
                    'B' => 'Policies only work in CSS',
                    'C' => 'Databases cannot store users',
                    'D' => 'Routes cannot redirect',
                ], 'A', 'Buttons and links improve UX, but the server must enforce authorization.'),
                $this->task('Advanced', 'Write the four-route proof', 'For one feature, test guest access, valid create, invalid create, and unauthorized update or delete.'),
            ],
            'laravel-full-stack' => [
                $this->mcq('Basics', 'What does an Inertia controller pass to React?', [
                    'A' => 'Page props',
                    'B' => 'Raw database credentials',
                    'C' => 'Compiled PHP bytecode',
                    'D' => 'Only CSS variables',
                ], 'A', 'Inertia pages receive props from Laravel controllers, similar to server-rendered view data.'),
                $this->mcq('Basics', 'What should never be sent as an Inertia prop?', [
                    'A' => 'Secrets or private credentials',
                    'B' => 'A page title',
                    'C' => 'A validation error',
                    'D' => 'A count of public records',
                ], 'A', 'Inertia props become available to the browser, so secrets must stay server-side.'),
                $this->mcq('Basics', 'What does Ziggy help React use?', [
                    'A' => 'Laravel route names',
                    'B' => 'Database migrations',
                    'C' => 'Queue retry counts',
                    'D' => 'PHP namespaces',
                ], 'A', 'Ziggy exposes Laravel route names so frontend code can generate URLs consistently.'),
                $this->task('Basics', 'Map a full-stack request', 'Choose one button in the app and trace the React event, route helper, controller action, model write, and redirect.'),
                $this->mcq('Intermediate', 'Why use pagination for index pages?', [
                    'A' => 'To avoid loading too many rows at once',
                    'B' => 'To disable authorization',
                    'C' => 'To skip validation',
                    'D' => 'To remove all filters',
                ], 'A', 'Pagination keeps index pages predictable as data grows.'),
                $this->mcq('Intermediate', 'What is a resource controller good for?', [
                    'A' => 'A conventional CRUD action set',
                    'B' => 'Running npm commands',
                    'C' => 'Encrypting every database row automatically',
                    'D' => 'Replacing all tests',
                ], 'A', 'Resource controllers provide conventional methods like index, store, update, and destroy.'),
                $this->mcq('Intermediate', 'When should React state be local?', [
                    'A' => 'When it only controls one component interaction',
                    'B' => 'When it stores server secrets',
                    'C' => 'When it replaces database persistence',
                    'D' => 'When it bypasses validation',
                ], 'A', 'Local UI state is appropriate for temporary component behavior; durable data belongs on the server.'),
                $this->task('Intermediate', 'Create one filterable list', 'Add a server-backed filter to an index page and keep the selected filter visible after navigation.'),
                $this->mcq('Advanced', 'Why fake queues in tests?', [
                    'A' => 'To assert jobs were dispatched without running slow side effects',
                    'B' => 'To disable all assertions',
                    'C' => 'To make migrations optional',
                    'D' => 'To expose secrets to React',
                ], 'A', 'Queue fakes let tests prove dispatch behavior while avoiding slow or external work.'),
                $this->mcq('Advanced', 'What should happen after a successful POST in an Inertia app?', [
                    'A' => 'Redirect to a GET route',
                    'B' => 'Render a blank PHP file',
                    'C' => 'Leave duplicate form submissions unhandled',
                    'D' => 'Commit the SQLite database',
                ], 'A', 'Redirect-after-POST keeps browser refreshes and navigation predictable.'),
                $this->mcq('Advanced', 'Why inspect network requests while debugging Inertia?', [
                    'A' => 'To see props, validation errors, redirects, and status codes',
                    'B' => 'To edit migrations in the browser',
                    'C' => 'To replace feature tests',
                    'D' => 'To bypass policies',
                ], 'A', 'The network tab shows the real request and response path that drives Inertia updates.'),
                $this->task('Advanced', 'Add one end-to-end proof note', 'After building a feature, write the exact manual browser flow and automated test command that prove it works.'),
            ],
            'modern-laravel' => [
                $this->mcq('Basics', 'What does config:cache change?', [
                    'A' => 'It compiles configuration into a cached file',
                    'B' => 'It creates database indexes',
                    'C' => 'It restarts Vite',
                    'D' => 'It deletes queues',
                ], 'A', 'config:cache makes config reads faster but means environment changes need a cache refresh.'),
                $this->mcq('Basics', 'What does route:cache require?', [
                    'A' => 'Routes that can be serialized',
                    'B' => 'Only guest routes',
                    'C' => 'No controllers at all',
                    'D' => 'A public database',
                ], 'A', 'Route caching stores route definitions, so routes must be compatible with serialization.'),
                $this->mcq('Basics', 'Why run migrations before workers handle new jobs?', [
                    'A' => 'Jobs may depend on new tables or columns',
                    'B' => 'Workers compile Tailwind',
                    'C' => 'Migrations send emails',
                    'D' => 'Queues replace databases',
                ], 'A', 'Workers can execute code that expects the latest schema, so deploy order matters.'),
                $this->task('Basics', 'Write a deploy order note', 'Draft the order for code sync, Composer install, npm build, migrate, cache, worker restart, and health check.'),
                $this->mcq('Intermediate', 'What does withoutOverlapping protect in scheduled tasks?', [
                    'A' => 'The same scheduled command running twice at once',
                    'B' => 'All controller validation',
                    'C' => 'Every database deadlock',
                    'D' => 'Frontend route changes',
                ], 'A', 'withoutOverlapping uses a lock so a slow scheduled run does not overlap the next run.'),
                $this->mcq('Intermediate', 'Why use cache locks?', [
                    'A' => 'To coordinate exclusive work across processes',
                    'B' => 'To style badges',
                    'C' => 'To replace all transactions',
                    'D' => 'To hide route names',
                ], 'A', 'Cache locks help prevent concurrent workers or requests from doing the same sensitive work twice.'),
                $this->mcq('Intermediate', 'What does a transaction protect?', [
                    'A' => 'A group of database writes that should succeed or fail together',
                    'B' => 'A CSS animation',
                    'C' => 'A Vite hot reload',
                    'D' => 'A route list command',
                ], 'A', 'Transactions keep related database changes consistent when a later step fails.'),
                $this->task('Intermediate', 'Wrap a multi-write flow', 'Find or create a flow with two related writes, wrap it in a transaction, and test the failure behavior.'),
                $this->mcq('Advanced', 'Why monitor failed jobs?', [
                    'A' => 'They reveal background work that users may not see fail immediately',
                    'B' => 'They replace logs',
                    'C' => 'They prove CSS loaded',
                    'D' => 'They delete old sessions',
                ], 'A', 'Failed jobs can silently break important follow-up work, so they need inspection and retry strategy.'),
                $this->mcq('Advanced', 'What should a health endpoint avoid doing?', [
                    'A' => 'Slow destructive or side-effect-heavy work',
                    'B' => 'Returning a simple OK signal',
                    'C' => 'Checking lightweight dependencies',
                    'D' => 'Being easy to curl',
                ], 'A', 'Health checks should be fast and safe because they may run frequently.'),
                $this->mcq('Advanced', 'Why document rollback signals?', [
                    'A' => 'So the team knows when a deploy is unsafe to keep live',
                    'B' => 'So tests can be deleted',
                    'C' => 'So secrets can be committed',
                    'D' => 'So migrations become optional',
                ], 'A', 'Rollback signals turn vague concern into concrete thresholds for reverting or fixing forward.'),
                $this->task('Advanced', 'Create an operations checklist', 'Write a short checklist for logs, queues, migrations, assets, config cache, and one HTTP health proof.'),
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
