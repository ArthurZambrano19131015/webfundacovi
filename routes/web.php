<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\UserManager;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/admin/usuarios', UserManager::class)->name('admin.usuarios');

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';