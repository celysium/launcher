<?php

use Illuminate\Support\Facades\Route;

Route::view('launcher', 'launcher::index')->middleware('web');