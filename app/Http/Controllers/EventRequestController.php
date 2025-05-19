<?php

namespace App\Http\Controllers;

use App\Models\EventRequest;
use Illuminate\Http\Request;

class EventRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'namakegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $validated['user_id'] = auth()->id();

        $eventRequest = EventRequest::create($validated);

        return response()->json([
            'message' => 'Event request berhasil dikirim',
            'data' => $eventRequest
        ], 201);
    }
} 