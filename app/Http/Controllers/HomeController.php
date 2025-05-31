<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = Category::all();
        
        // Sederhanakan query dulu untuk debugging
        $eventsQuery = Event::query()
            ->with(['user', 'categories'])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            });

        // Log query SQL untuk debugging
        Log::info('Events Query:', [
            'sql' => $eventsQuery->toSql(),
            'bindings' => $eventsQuery->getBindings()
        ]);

        $events = $eventsQuery->paginate(12)->withQueryString();

        // Log hasil query
        Log::info('Events Result:', [
            'count' => $events->count(),
            'total' => $events->total(),
            'first_event' => $events->first() ? $events->first()->toArray() : null
        ]);

        $view = auth()->check() ? 'Dashboard' : 'DashboardUser';

        return Inertia::render($view, [
            'auth' => ['user' => auth()->user()],
            'dataevent' => $events,
            'category' => $categories,
            'filters' => [
                'search' => $search,
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }
} 