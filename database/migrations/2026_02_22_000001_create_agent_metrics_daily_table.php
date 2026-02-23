<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_metrics_daily', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->cascadeOnDelete();
            $table->date('date');
            $table->integer('total_messages')->default(0);
            $table->integer('user_messages')->default(0);
            $table->integer('agent_messages')->default(0);
            $table->integer('total_conversations')->default(0);
            $table->bigInteger('total_input_tokens')->default(0);
            $table->bigInteger('total_output_tokens')->default(0);
            $table->bigInteger('total_tokens')->default(0);
            $table->integer('avg_response_tokens')->default(0);
            $table->integer('unique_users')->default(0);
            $table->integer('error_count')->default(0);
            $table->timestamps();

            $table->unique(['agent_id', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_metrics_daily');
    }
};
