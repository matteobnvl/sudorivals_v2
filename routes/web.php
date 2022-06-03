<?php

use App\Route;

Route::get(['/', 'App\Controllers\HomeController@index'])->name('Accueil');
Route::get(['/contact', 'App\Controllers\ContactController@index'])->name('Contact');
Route::get(['/Apropos', 'App\Controllers\AproposController@index'])->name('A propos');

// Connexion - Déconnexion
Route::get(['/connexion', 'App\Controllers\AuthController@login'])->name('Connexion');
Route::get(['/deconnexion', 'App\Controllers\AuthController@logout'])->name('Deconnexion');

// Private
Route::get(['/tableau-de-bord', 'App\Controllers\DashboardController@index'])->name('Tableau de bord');