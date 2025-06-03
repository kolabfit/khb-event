<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentDownloadController;
use App\Http\Controllers\EventHistoryController;
use App\Http\Controllers\EventRequestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\TicketValidationController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\UserTicketDownloadController;

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

// Home route with search functionality
Route::get('/', [HomeController::class, 'index'])->name('home');

// Event Request Routes
Route::post('/api/event-requests', [EventRequestController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('event-requests.store');

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

        // Ticket validation routes
        Route::get('/validate-ticket', [TicketValidationController::class, 'showValidationPage'])
            ->name('admin.validate-ticket');
        Route::post('/validate-ticket', [TicketValidationController::class, 'validateTicket'])
            ->name('admin.validate-ticket.post');
    });

Route::get('/filament/reports/export', [ReportExportController::class, 'export'])
    ->middleware(['auth'])   // sesuaikan middleware Filament Anda
    ->name('report.export');

Route::get('/dashboard', function () {
    $data = Event::all()->load('user');
    $category = Category::all();
    return Inertia::render(auth()->check() ? 'DashboardUser' : 'Dashboard')->with(
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
    return Inertia::render('DetailEvent')->with([
        'event' => $event,
        'auth' => [
            'user' => auth()->user(),
        ],
    ]);
})->name('detail-events');

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

    Route::get('/my-tickets/{ticket}/download', [UserTicketDownloadController::class, 'download'])
        ->name('user.tickets.download');

    Route::get('/payments/{ticket}/confirm', [OrderController::class, 'showPaymentConfirmation'])
        ->name('payments.confirm');
    Route::post('/payments/confirm', [OrderController::class, 'confirmStore'])
        ->name('payments.confirm.store');
    Route::get('/payments/{payment}/download', PaymentDownloadController::class)
        ->name('user.payments.download');

    Route::get('/event-history', [EventHistoryController::class, 'index'])->name('event.history');
    Route::get('/payment-history', [PaymentHistoryController::class, 'index'])->name('payment.history');
    Route::post('/payment-history/confirm', [EventHistoryController::class, 'confirmStore'])
        ->name('payment.history.confirm.store');
    Route::get('/payment-history/{payment}', [EventHistoryController::class, 'show'])->name('payment.history.show');
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

Route::get('/tickets/{ticket}/qr', [TicketController::class, 'showQrCode'])->name('tickets.qr');
Route::get('/tickets/{ticket}/qr-code', [QrCodeController::class, 'generate'])->name('tickets.qr-code');

require __DIR__ . '/auth.php';
