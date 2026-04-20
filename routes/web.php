<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\ApiarioManager;
use App\Livewire\Admin\ColmenaManager;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/admin/usuarios', UserManager::class)->name('admin.usuarios');
    Route::get('/admin/apiarios', ApiarioManager::class)->name('admin.apiarios');
    Route::get('/admin/colmenas', ColmenaManager::class)->name('admin.colmenas');
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';