<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'status' => 'ok',
        'message' => 'This is the API. Endpoints live under /api. See README.md and API_ENDPOINTS.md.',
    ]);
});
