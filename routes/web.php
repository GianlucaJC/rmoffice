<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//onlylog
Route::middleware('auth')->group(function () {
	//ajax routing
	Route::post('cerca_fo', 'App\Http\Controllers\AjaxController@cerca_fo');
	Route::post('cerca_azi', 'App\Http\Controllers\AjaxController@cerca_azi');
	Route::post('save_note', 'App\Http\Controllers\AjaxController@save_note');
	
	Route::post('ins_frt', 'App\Http\Controllers\AjaxController@ins_frt');
	
	//
	
	
	Route::get('/main_view', [ 'as' => 'main_view', 'uses' => 'App\Http\Controllers\MainController@main_view']);

	Route::post('/main_view', [ 'as' => 'main_view', 'uses' => 'App\Http\Controllers\MainController@main_view']);

});
require __DIR__.'/auth.php';
