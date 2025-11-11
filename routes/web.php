<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // 👤 PROFILO UTENTE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🧾 PRENOTAZIONI (ORDINI)
    Route::get('/prenotazioni', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/prenotazioni/crea', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/prenotazioni', [OrderController::class, 'store'])->name('orders.store');

    // 🔁 SLOT ORARI (AJAX)
    Route::get('/prenotazioni/slots', [OrderController::class, 'slots'])->name('orders.slots');
});

require __DIR__.'/auth.php';
