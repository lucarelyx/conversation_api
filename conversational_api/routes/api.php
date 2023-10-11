<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversationController;

Route::post('/conversation', [ConversationController::class, 'processRequest']);

