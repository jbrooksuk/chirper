<?php

use App\Http\Controllers\Api\ChirpController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    /**
     * Get User.
     * @group Users
     * @authenticated
     * @response status=200 {"id" : 1, "name": "James Brooks"}
     */
    Route::get('/user', fn (Request $request) => $request->user());

    Route::apiResource('chirps', ChirpController::class);
});
