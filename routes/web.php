<?php

use App\Http\Controllers\NeracaController;
use Illuminate\Support\Facades\Route;

Route::get('/neracas', [NeracaController::class, 'index'])->name('neraca.index');
