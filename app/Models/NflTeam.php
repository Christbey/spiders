<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflTeam extends Model
{
    public function homeGames()
    {
        return $this->hasMany(NflGame::class, 'team_home_id');
    }

    public function awayGames()
    {
        return $this->hasMany(NflGame::class, 'team_away_id');
    }
}
