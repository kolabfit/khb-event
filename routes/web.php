<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportExportController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Admin-only
// Route::middleware(['auth','role:admin'])->get('/admin', function () {
//     return view('admin.dashboard');
// })->name('admin.dashboard');

// User-only
Route::middleware(['auth', 'role:user'])->get('/user', function () {
    return view('user.dashboard');
})->name('user.dashboard');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')               // atau sesuai prefix Filament-mu
    ->group(function () {
        Route::get(
            '/tickets/{ticket}/download',
            [TicketController::class, 'download']
        )
            ->name('tickets.download');
    });

Route::get('/filament/reports/export', [ReportExportController::class, 'export'])
    ->middleware(['auth'])   // sesuaikan middleware Filament Anda
    ->name('report.export');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
