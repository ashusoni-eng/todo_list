<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;

Route::get('/', [TasksController::class,'index']);
Route::get('/show_all', [TasksController::class,'show_all']);
Route::post('/task/add',[TasksController::class,'add']);
Route::delete('/task/delete/{id}',[TasksController::class,'delete']);
Route::put('/task/update/{id}',[TasksController::class,'update']);