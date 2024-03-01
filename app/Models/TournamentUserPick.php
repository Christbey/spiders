<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentUserPick extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'game_id', 'selected_team_id', 'region', 'round'];

    public function user()
    {
        return $this->belongsTo(User::class); // Assuming you're using the default User model
    }

    public function game()
    {
        return $this->belongsTo(TournamentGame::class, 'game_id');
    }

    public function selectedTeam()
    {
        return $this->belongsTo(TournamentTeam::class, 'selected_team_id');
    }
}
