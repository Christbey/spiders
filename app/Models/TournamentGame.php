<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentGame extends Model
{
    use HasFactory;

    protected $fillable = ['team1_id', 'team2_id', 'round', 'region', 'winner_team_id'];

    public function team1()
    {
        return $this->belongsTo(TournamentTeam::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(TournamentTeam::class, 'team2_id');
    }

    public function winner()
    {
        return $this->belongsTo(TournamentTeam::class, 'winner_team_id');
    }

    public function userPicks()
    {
        return $this->hasMany(TournamentUserPick::class, 'game_id');
    }
}
