<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('agent_type')->unique();
            $table->text('system_prompt');
            $table->string('model')->default('gpt-4o-mini');
            $table->string('provider')->default('openai');
            $table->decimal('temperature', 2, 1)->default(0.7);
            $table->integer('max_tokens')->default(4096);
            $table->integer('max_steps')->default(10);
            $table->string('vector_store_id')->nullable();
            $table->integer('max_conversation_messages')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
