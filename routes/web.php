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
Route::get(['/details','App\Controllers\ReadController@index'])-> name('Details');
Route::get(['/modifier','App\Controllers\UpdateController@index'])->name('Modifier');
Route::get(['/supprimer','App\Controllers\DeleteController@index'])->name('Supprimer');
Route::get(['/ajouter-utilisateur','App\Controllers\CreateUserController@index'])->name('CreateUser');

Route::get(['/ajouter-role','App\Controllers\CreateRoleController@index'])->name('CreateRole');
Route::get(['/details-role','App\Controllers\ReadRoleController@index'])->name('VoirRole');