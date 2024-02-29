<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_teams', function (Blueprint $table) {
            $table->id();
            $table->string('api_name')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_teams');
    }
}
