<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentTeam extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'seed', 'region'];

    public function gamesAsTeam1()
    {
        return $this->hasMany(TournamentGame::class, 'team1_id');
    }

    public function gamesAsTeam2()
    {
        return $this->hasMany(TournamentGame::class, 'team2_id');
    }

    public function picks()
    {
        return $this->hasMany(TournamentUserPick::class, 'selected_team_id');
    }
}
