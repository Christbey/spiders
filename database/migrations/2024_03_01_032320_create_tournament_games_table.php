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
        Schema::create('tournament_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team1_id')->constrained('tournament_teams');
            $table->foreignId('team2_id')->constrained('tournament_teams');
            $table->integer('round');
            $table->string('region');
            $table->foreignId('winner_team_id')->nullable()->constrained('tournament_teams');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_games');
    }
};
