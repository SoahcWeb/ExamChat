<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AskController;

Route::get('/', [AskController::class, 'index']);
