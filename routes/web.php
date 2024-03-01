<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

use App\Http\Controllers\TournamentController;

// Display form
Route::get('/tournament/picks', [TournamentController::class, 'showPicksForm'])->name('tournament.showPicksForm');

// Store picks
Route::post('/tournament/picks', [TournamentController::class, 'storePicks'])->name('tournament.storePicks');

Route::get('tournament/handleRoundAndRegion', [TournamentController::class, 'handleRoundAndRegion'])->name('tournament.handleRoundAndRegion');
