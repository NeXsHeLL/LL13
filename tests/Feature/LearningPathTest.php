<?php

namespace Tests\Feature;

use App\LearningQuestFactory;
use App\Models\LearningCheckpoint;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningPathTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_learning_paths(): void
    {
        $this->post(route('learning-paths.store'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_create_learning_paths(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('learning-paths.store'), [
                'title' => 'Build a Laravel 13 learning app',
                'focus_area' => 'Laravel',
                'outcome' => 'Understand migrations, controllers, Inertia, and tests.',
                'status' => 'active',
                'weekly_minutes' => 240,
                'confidence' => 3,
                'target_date' => now()->addMonth()->toDateString(),
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('learning_paths', [
            'user_id' => $user->id,
            'title' => 'Build a Laravel 13 learning app',
            'status' => 'active',
        ]);
    }

    public function test_learning_path_validation_rejects_invalid_payloads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('learning-paths.store'), [
                'title' => '',
                'focus_area' => '',
                'outcome' => '',
                'status' => 'unknown',
                'weekly_minutes' => 1,
                'confidence' => 9,
                'target_date' => now()->subDay()->toDateString(),
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors(['title', 'focus_area', 'outcome', 'status', 'weekly_minutes', 'confidence', 'target_date']);
    }

    public function test_users_can_log_learning_progress(): void
    {
        $user = User::factory()->create();
        $path = LearningPath::factory()->for($user)->create();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('learning-paths.logs.store', $path), [
                'learned_on' => now()->toDateString(),
                'minutes' => 50,
                'topic' => 'Route model binding',
                'reflection' => 'Policies keep ownership checks close to the model.',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('learning_logs', [
            'learning_path_id' => $path->id,
            'minutes' => 50,
            'topic' => 'Route model binding',
        ]);
    }

    public function test_users_can_add_learning_checkpoints(): void
    {
        $user = User::factory()->create();
        $path = LearningPath::factory()->for($user)->create();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('learning-paths.checkpoints.store', $path), [
                'title' => 'Create a migration and explain each column',
                'notes' => 'Focus on foreign keys and indexes.',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('learning_checkpoints', [
            'learning_path_id' => $path->id,
            'title' => 'Create a migration and explain each column',
            'is_complete' => false,
            'position' => 1,
        ]);
    }

    public function test_dashboard_exposes_the_guided_learning_catalog(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();

        $catalog = $response->inertiaProps('questCatalog');

        $this->assertCount(4, $catalog);
        $this->assertSame(
            ['php-foundations', 'laravel-foundations', 'laravel-full-stack', 'modern-laravel'],
            collect($catalog)->pluck('id')->all(),
        );
    }

    public function test_guided_learning_catalog_contains_the_first_batch_activity_depth(): void
    {
        $catalog = app(LearningQuestFactory::class)->catalog();

        $this->assertGreaterThanOrEqual(50, collect($catalog)->sum('activity_count'));
        $this->assertTrue(collect($catalog)->every(fn (array $quest): bool => $quest['activity_count'] >= 10));
    }

    public function test_every_guided_mcq_includes_an_explanation(): void
    {
        $learningQuestFactory = app(LearningQuestFactory::class);

        foreach ($learningQuestFactory->ids() as $questId) {
            $mcqs = collect($learningQuestFactory->get($questId)['checkpoints'])
                ->where('activity_type', 'mcq');

            $this->assertGreaterThan(0, $mcqs->count());
            $this->assertTrue(
                $mcqs->every(fn (array $checkpoint): bool => filled($checkpoint['explanation'] ?? null)),
                "Quest [{$questId}] has an MCQ without an explanation.",
            );
        }
    }

    public function test_users_can_generate_curated_learning_quests(): void
    {
        $user = User::factory()->create();
        $catalog = app(LearningQuestFactory::class)->catalog();

        foreach ($catalog as $quest) {
            $this->actingAs($user)
                ->from(route('dashboard'))
                ->post(route('learning-paths.quest.store'), [
                    'quest' => $quest['id'],
                ])
                ->assertRedirect(route('dashboard'));

            $this->assertDatabaseHas('learning_paths', [
                'user_id' => $user->id,
                'title' => $quest['title'],
                'status' => 'active',
            ]);

            $path = LearningPath::where('user_id', $user->id)->where('title', $quest['title'])->firstOrFail();

            $this->assertSame($quest['activity_count'], $path->checkpoints()->count());
            $this->assertGreaterThan(0, $path->checkpoints()->where('activity_type', 'mcq')->count());
            $this->assertGreaterThan(0, $path->checkpoints()->where('activity_type', 'task')->count());
        }
    }

    public function test_dashboard_does_not_expose_mcq_answer_keys(): void
    {
        $user = User::factory()->create();
        $path = LearningPath::factory()->for($user)->create();
        LearningCheckpoint::factory()->mcq()->for($path)->create([
            'correct_option' => 'A',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();

        $checkpoint = data_get($response->inertiaProps(), 'paths.0.checkpoints.0');

        $this->assertIsArray($checkpoint);
        $this->assertArrayNotHasKey('correct_option', $checkpoint);
    }

    public function test_users_can_toggle_learning_checkpoints(): void
    {
        $user = User::factory()->create();
        $path = LearningPath::factory()->for($user)->create();
        $checkpoint = LearningCheckpoint::factory()->for($path)->create([
            'is_complete' => false,
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('learning-paths.checkpoints.update', [$path, $checkpoint]), [
                'is_complete' => true,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('learning_checkpoints', [
            'id' => $checkpoint->id,
            'is_complete' => true,
        ]);
    }

    public function test_mcq_checkpoints_complete_only_when_answered_correctly(): void
    {
        $user = User::factory()->create();
        $path = LearningPath::factory()->for($user)->create();
        $checkpoint = LearningCheckpoint::factory()->mcq()->for($path)->create([
            'correct_option' => 'A',
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('learning-paths.checkpoints.update', [$path, $checkpoint]), [
                'user_answer' => 'B',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('learning_checkpoints', [
            'id' => $checkpoint->id,
            'user_answer' => 'B',
            'is_complete' => false,
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('learning-paths.checkpoints.update', [$path, $checkpoint]), [
                'user_answer' => 'A',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('learning_checkpoints', [
            'id' => $checkpoint->id,
            'user_answer' => 'A',
            'is_complete' => true,
        ]);
    }

    public function test_users_cannot_update_another_users_learning_path(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $path = LearningPath::factory()->for($owner)->create([
            'title' => 'Private path',
        ]);

        $this->actingAs($otherUser)
            ->put(route('learning-paths.update', $path), [
                'title' => 'Changed path',
                'focus_area' => $path->focus_area,
                'outcome' => $path->outcome,
                'status' => 'completed',
                'weekly_minutes' => $path->weekly_minutes,
                'confidence' => $path->confidence,
                'target_date' => $path->target_date?->toDateString(),
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('learning_paths', [
            'id' => $path->id,
            'title' => 'Private path',
        ]);
    }

    public function test_users_cannot_update_another_users_learning_checkpoint(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $path = LearningPath::factory()->for($owner)->create();
        $checkpoint = LearningCheckpoint::factory()->for($path)->create([
            'is_complete' => false,
        ]);

        $this->actingAs($otherUser)
            ->put(route('learning-paths.checkpoints.update', [$path, $checkpoint]), [
                'is_complete' => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('learning_checkpoints', [
            'id' => $checkpoint->id,
            'is_complete' => false,
        ]);
    }
}
