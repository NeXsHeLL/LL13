<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('learning_checkpoints', function (Blueprint $table) {
            $table->string('activity_type')->default('task')->after('notes');
            $table->string('difficulty')->default('basics')->after('activity_type');
            $table->text('prompt')->nullable()->after('difficulty');
            $table->json('options')->nullable()->after('prompt');
            $table->string('correct_option')->nullable()->after('options');
            $table->string('user_answer')->nullable()->after('correct_option');
            $table->text('explanation')->nullable()->after('user_answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_checkpoints', function (Blueprint $table) {
            $table->dropColumn([
                'activity_type',
                'difficulty',
                'prompt',
                'options',
                'correct_option',
                'user_answer',
                'explanation',
            ]);
        });
    }
};
