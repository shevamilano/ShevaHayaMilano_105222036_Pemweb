<?php

namespace App\Jawaban;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Event;

class NomorDua {
    public function submit(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start'
        ]);

        try {
            Event::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'start' => $request->start,
                'end' => $request->end
            ]);

            return redirect()
                ->route('event.home')
                ->with('message', ['Jadwal berhasil ditambahkan!', 'success']);
        } catch (\Exception $e) {
            return redirect()
                ->route('event.home')
                ->with('message', ['Jadwal gagal ditambahkan!', 'danger']);
        }
    }
}