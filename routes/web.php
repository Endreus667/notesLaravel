<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\CheckIsLogged;
use App\Http\Middleware\CheckIsNotLogged;
use Illuminate\Support\Facades\Route;

// Rota principal
Route::get('/main', [MainController::class, 'main']);

// Rotas de autenticação - usuário não logado
Route::middleware([CheckIsNotLogged::class])->group(function() {
    Route::get('/login', [AuthController::class, 'login']);
    // Aplicando o middleware throttle para limitar as tentativas
    Route::post('/loginSubmit', [AuthController::class, 'loginSubmit'])->middleware('throttle:5,1'); // 5 tentativas por 1 minuto
});

// Rotas do app - usuário logado
Route::middleware([CheckIsLogged::class])->group(function() {
    Route::get('/', [MainController::class, 'index'])->name('home');
    Route::get('/newNote', [MainController::class, 'newNote'])->name('new');
    Route::post('/newNoteSubmit', [MainController::class, 'newNoteSubmit'])->name('newNoteSubmit');

    // Editar nota
    Route::get('/editNote/{id}', [MainController::class, 'editNote'])->name('edit');
    Route::post('/editNoteSubmit/{id}', [MainController::class, 'editNoteSubmit'])->name('editNoteSubmit');

    // Deletar nota
    Route::get('/deleteNote/{id}', [MainController::class, 'deleteNote'])->name('delete');
    Route::get('/deleteNoteConfirm/{id}', [MainController::class, 'deleteNoteConfirm'])->name('deleteConfirm');
});

// Rota de logout (fora do grupo de usuário logado)
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
