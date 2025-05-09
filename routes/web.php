<?php

use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\OrderController;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
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
    $data = Event::all()->load('user');
    $category = Category::all();
    return Inertia::render('Dashboard')->with(
        [
            'dataevent' => $data,
            'category' => $category,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]
    );
})->middleware(['auth', 'verified'])->name('dashboard');



// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get(
//         '/admin/settings',
//         [ProfileController::class, 'edit']
//     )
//         ->name('filament.admin.settings');

//     Route::patch(
//         '/admin/settings',
//         [ProfileController::class, 'update']
//     )
//         ->name('filament.admin.settings.update');

//     Route::delete(
//         '/profile',                   // akun dihapus lewat URL /profile
//         [ProfileController::class, 'destroy']
//     )
//         ->name('profile.destroy');
// });

Route::get('/detail-event', function (Request $request) {
    $event = Event::where('id', $request->query('id'))->with('user', 'categories')->first();
    return Inertia::render('DetailEvent')->with(
        ['event' => $event]
    );
})->middleware(['auth', 'verified'])->name('detail-events');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // GET form order
    Route::get('/order-event/order', [OrderController::class, 'create'])
        ->name('order-event.order');

    // POST simpan order
    Route::post('/order-event/order', [OrderController::class, 'store'])
        ->name('order-event.order.store');
});

// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/payments/{ticket}/confirm', [OrderController::class, 'confirm'])
        ->name('payments.confirm');
    Route::post('/payments/confirm', [OrderController::class, 'confirmStore'])
        ->name('payments.confirm.store');
});

// Halaman list events, optional filter ?category=NamaKategori
Route::get('/events', function (Request $request) {
    $categories = Category::all();

    // Query builder
    $query = Event::with('categories', 'user');

    if ($request->filled('category')) {
        $query->whereHas('categories', function ($q) use ($request) {
            $q->where('name', $request->category);
        });
    }

    $events = $query->get();

    return Inertia::render('EventsPage', [
        'events' => $events,
        'categories' => $categories,
        'selectedCategory' => $request->category,
    ]);
})->name('events');

require __DIR__ . '/auth.php';
