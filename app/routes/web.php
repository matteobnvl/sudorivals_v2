<?php

use App\Route;

Route::get(['/', 'App\Controllers\HomeController@index'])->name('Accueil');