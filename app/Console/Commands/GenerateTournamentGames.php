<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentTeam;
use App\Models\TournamentGame;

class GenerateTournamentGames extends Command
{
    protected $signature = 'generate:tournament';
    protected $description = 'Generates the first round of tournament games based on team seeds and regions.';

    public function handle()
    {
        $regions = TournamentTeam::select('region')->distinct()->pluck('region');

        foreach ($regions as $region) {
            $teams = TournamentTeam::where('region', $region)->orderBy('seed')->get();

            // Assuming a standard 16-team bracket per region for simplicity
            for ($i = 0; $i < $teams->count() / 2; $i++) {
                $team1 = $teams[$i];
                $team2 = $teams[$teams->count() - $i - 1];

                TournamentGame::create([
                    'team1_id' => $team1->id,
                    'team2_id' => $team2->id,
                    'round' => 1,
                    // Make sure the region is correctly passed here. It should not be null.
                    'region' => $team1->region, // Assuming both teams in a match are from the same region
                ]);

                $this->info("Created game: {$team1->name} vs {$team2->name} in {$region}");
            }
        }
    }
}
