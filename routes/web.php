<?php

use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoteController;
use App\Models\Vote;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Index', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});




Route::get('/create', [ElectionController::class, 'create'])->name('election.create');
Route::get('/created/{uuid}', [ElectionController::class, 'created'])->name('election.created');

Route::post('/store', [ElectionController::class, 'store'])->name('election.store');

Route::get('/e/{uuid}', [ElectionController::class, 'index'])->name('election.vote');
Route::get('/r/{uuid}', [ElectionController::class, 'results'])->name('election.results');
Route::get('/r/{uuid}/{votecode}', [ElectionController::class, 'resultsWithCode'])->name('election.results.code');


Route::post("/v", [VoteController::class, 'store'])->name('vote.store');




Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
