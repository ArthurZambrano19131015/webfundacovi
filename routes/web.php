<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\UserManager;
use App\Livewire\Admin\ApiarioManager;
use App\Livewire\Admin\ColmenaManager;
use App\Livewire\Admin\EstandarManager;
use App\Livewire\Apicultor\CosechaManager;
use App\Livewire\Apicultor\LoteCalidadManager;
use App\Livewire\Dashboard;
use App\Livewire\Apicultor\ProductoManager;
use App\Livewire\Admin\ReporteManager;
use App\Livewire\Publico\LandingPage;

Route::get('/', LandingPage::class)->name('inicio');

Route::middleware(['auth'])->group(function () {
    
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('/admin/usuarios', UserManager::class)->name('admin.usuarios');
    Route::get('/admin/apiarios', ApiarioManager::class)->name('admin.apiarios');
    Route::get('/admin/colmenas', ColmenaManager::class)->name('admin.colmenas');
    Route::get('/admin/reportes', ReporteManager::class)->name('admin.reportes');
    Route::view('profile', 'profile')->name('profile');
    Route::get('/admin/estandares', EstandarManager::class)->name('admin.estandares');
    Route::get('/apicultor/cosechas', CosechaManager::class)->name('apicultor.cosechas');
    Route::get('/apicultor/calidad', LoteCalidadManager::class)->name('apicultor.calidad');
    Route::get('/apicultor/productos', ProductoManager::class)->name('apicultor.productos');
});

require __DIR__.'/auth.php';