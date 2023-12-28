<?php

use App\Route;

Route::get(['/', 'App\Controllers\HomeController@index'])->name('app_home');

// Route Authentification

Route::get(['/login', 'App\Controllers\AuthentificationController@login'])->name('app_login');
Route::get(['/logout', 'App\Controllers\AuthentificationController@logout'])->name('app_logout');
Route::get(['/register', 'App\Controllers\AuthentificationController@register'])->name('app_register');
Route::get(['/new-password', 'App\Controllers\AuthentificationController@ResetPassword'])->name('app_reset_password');

// Route Dashboard
Route::get(['/dashboard', 'App\Controllers\DashboardController@index'])->name('app_dashboard');
Route::get(['/classement', 'App\Controllers\DashboardController@classement'])->name('app_classement');
Route::get(['/mes-sudokus', 'App\Controllers\DashboardController@allSudoku'])->name('app_all_sudoku');

// Route Contact
Route::get(['/contact', 'App\Controllers\ContactController@index'])->name('app_contact');
Route::get(['/contacts', 'App\Controllers\ContactController@contacts'])->name('app_contacts');

// Route Profil
Route::get(['/profil', 'App\Controllers\ProfilController@index'])->name('app_profil');
Route::get(['/suppression-compte', 'App\Controllers\ProfilController@delete'])->name('app_delete_user');

// Route Game 
Route::get(['/game/{id_partie}', 'App\Controllers\GameController@index'])->name('app_game');
Route::get(['/insert', 'App\Controllers\GameController@insert'])->name('app_insert');
Route::get(['/delete', 'App\Controllers\GameController@delete'])->name('app_delete');
Route::get(['/verif', 'App\Controllers\GameController@verif'])->name('app_verif');
Route::get(['/finish', 'App\Controllers\GameController@finish'])->name('app_finish');
Route::get(['/retry', 'App\Controllers\GameController@retry'])->name('app_retry');
Route::get(['/generate', 'App\Controllers\GameController@generate'])->name('app_generate');

// Route Multijoueur
Route::get(['/multijoueur', 'App\Controllers\MultijoueurController@multi'])->name('app_multi');
Route::get(['/attente', 'App\Controllers\MultijoueurController@attente'])->name('app_attente');
Route::get(['/game-multi/{id_duel}/{id_sudoku}', 'App\Controllers\MultijoueurController@gameMulti'])->name('app_game_multi');
Route::get(['/sudoku-adverse', 'App\Controllers\MultijoueurController@sudokeAdverse'])->name('app_sudoku_adverse');
Route::get(['/insert-multi', 'App\Controllers\MultijoueurController@insertMulti'])->name('app_insert_multi');
Route::get(['/delete-multi', 'App\Controllers\MultijoueurController@deleteMulti'])->name('app_delete_multi');
Route::get(['/vie', 'App\Controllers\MultijoueurController@vie'])->name('app_vie_adverse');
Route::get(['/win', 'App\Controllers\MultijoueurController@win'])->name('app_win');
Route::get(['/lose', 'App\Controllers\MultijoueurController@lose'])->name('app_lose');
Route::get(['/check-vainqueur', 'App\Controllers\MultijoueurController@checkVainqueur'])->name('app_check_vainqueur');
Route::get(['/verif-multi', 'App\Controllers\MultijoueurController@verifMulti'])->name('app_verif_multi');
Route::get(['/finish-multi', 'App\Controllers\MultijoueurController@finishMulti'])->name('app_finish_multi');
