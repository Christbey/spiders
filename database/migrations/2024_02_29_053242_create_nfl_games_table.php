<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflGamesTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_games', function (Blueprint $table) {
            $table->id();
            $table->string('team_away_name');
            $table->string('team_home_name');
            $table->integer('score_home');
            $table->integer('score_away');
            $table->integer('week');
            $table->string('game_day');
            $table->year('year');
            $table->string('type');
            $table->date('game_date');
            $table->foreignId('team_away_id')->constrained('nfl_teams')->onDelete('cascade');
            $table->foreignId('team_home_id')->constrained('nfl_teams')->onDelete('cascade');
            $table->string('team_home_record');
            $table->string('team_away_record');
            $table->string('game_id')->unique(); // Assuming game_id is unique
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_games');
    }
}
