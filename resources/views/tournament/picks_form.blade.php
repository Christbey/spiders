<form action="{{ route('tournament.handleRoundAndRegion') }}" method="GET">
    @csrf

    <div>
        <label for="round">Select Round:</label>
        <select name="round" id="round">
            <option value="">All Rounds</option>
            @foreach ($rounds as $round)
                <option value="{{ $round }}">{{ $round }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="region">Select Region:</label>
        <select name="region" id="region">
            <option value="">All Regions</option>
            @foreach ($regions as $region)
                <option value="{{ $region }}">{{ $region }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit">Filter</button>
</form>

<form action="{{ route('tournament.storePicks') }}" method="POST">
    @csrf

    <input type="hidden" name="round" value="{{ $round }}">
    <input type="hidden" name="region" value="{{ $region }}">
@foreach ($gamesForPicks as $game)
        <div>
            <input type="hidden" name="picks[{{ $game->id }}][game_id]" value="{{ $game->id }}">
            <input type="hidden" name="picks[{{ $game->id }}][region]" value="{{ $game->region }}">
            <input type="hidden" name="picks[{{ $game->id }}][round]" value="{{ $game->round }}">

            <label>{{ $game->team1->name }} vs {{ $game->team2->name }} {{$game->team2->seed}}</label>
            <select name="picks[{{ $game->id }}][selected_team_id]">
                <option value="{{ $game->team1_id }}">{{ $game->team1->name }}</option>
                <option value="{{ $game->team2_id }}">{{ $game->team2->name }}</option>
            </select>
        </div>
    @endforeach

    <button type="submit">Submit Picks</button>
</form>
