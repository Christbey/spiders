<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflGame extends Model
{
    protected $fillable = [
        'team_away_id',
        'team_home_id',
        'team_away_score',
        'team_home_score',
        'team_away_name',
        'team_home_name',
        'score_away',
        'score_home',
        'week',
        'game_day',
        'year',
        'type',
        'game_date',
        'team_away_record',
        'team_home_record',
        'game_id', // Only include this if you are allowing it to be set manually
    ];

    public function homeTeam()
    {
        return $this->belongsTo(NflTeam::class, 'team_home_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NflTeam::class, 'team_away_id');
    }
}

