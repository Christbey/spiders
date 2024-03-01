<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentGame;
use App\Models\TournamentUserPick;
use Auth;

class TournamentController extends Controller
{
    /**
     * Display the form for users to make their picks.
     */
    public function showPicksForm(Request $request)
    {

        $user = Auth::user();

        // Fetch unique rounds from tournament_user_picks for the user
        $rounds = TournamentUserPick::where('user_id', $user->id)->distinct('round')->pluck('round');

        // Fetch unique regions from tournament_user_picks for the user
        $regions = TournamentUserPick::where('user_id', $user->id)->distinct('region')->pluck('region');

        // Determine the selected round and region from the request
        $selectedRound = $request->input('round');
        $selectedRegion = $request->input('region');

        // Filter games based on the selected round and region
        $gamesForPicks = TournamentGame::query();

        if ($selectedRound) {
            $gamesForPicks->where('round', $selectedRound);
        }

        if ($selectedRegion) {
            $gamesForPicks->where('region', $selectedRegion);
        }

        $gamesForPicks = $gamesForPicks->get();

        return view('tournament.picks_form', compact('rounds', 'gamesForPicks', 'regions'));
        dd();
    }




    /**
     * Store the user's picks.
     */
    public function storePicks(Request $request)
    {
        $user = Auth::user(); // Ensure you're using Laravel's authentication to get the authenticated user

        // Assuming the request contains an array of picks
        $picks = $request->input('picks', []);

        // Iterate over each pick
        foreach ($picks as $pick) {
            if (is_array($pick) && isset($pick['game_id'], $pick['selected_team_id'], $pick['region'], $pick['round'])) {
                TournamentUserPick::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'game_id' => $pick['game_id'],
                    ],
                    [
                        'selected_team_id' => $pick['selected_team_id'],
                        'region' => $pick['region'],
                        'round' => $pick['round'],
                    ]
                );
            } else {
                // Handle invalid pick format
                // For example, log an error or return a response indicating a bad request
            }
        }

        // Redirect the user with a success message
        return back()->with('success', 'Your picks have been saved successfully!');
    }

    private function determineUserCurrentRound($userId)
    {
        $lastRoundPicked = TournamentUserPick::where('user_id', $userId)
            ->join('tournament_games', 'tournament_user_picks.game_id', '=', 'tournament_games.id')
            ->max('tournament_games.round');

        return $lastRoundPicked;
    }

    public function handleRoundAndRegion(Request $request)
    {
        $round = $request->input('round');
        $region = $request->input('region');

        // Fetch unique rounds and regions
        $rounds = TournamentGame::distinct('round')->pluck('round');
        $regions = TournamentGame::distinct('region')->pluck('region');

        // Fetch games based on selected round and region
        $gamesForPicks = TournamentGame::query();

        if ($round) {
            $gamesForPicks->where('round', $round);
        }

        if ($region) {
            $gamesForPicks->where('region', $region);
        }

        $gamesForPicks = $gamesForPicks->get();

        // Return the filtered games to the view along with rounds and regions
        return view('tournament.picks_form', compact('rounds', 'gamesForPicks', 'regions'));
    }




}

