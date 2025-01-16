<?php

namespace App\Jawaban;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Event;

class NomorEmpat {
    public function getJson() {
        $events = Event::with('user')->get();
        
        $jadwal = [];
        foreach($events as $event) {
            $jadwal[] = [
                'id' => $event->id,
                'title' => $event->name . ' - ' . $event->user->name, 
                'start' => $event->start,
                'end' => $event->end,
                'color' => $this->getEventColor($event->user_id)
            ];
        }

        return response()->json($jadwal);
    }

	private function getEventColor($userId)
	{
		if ($userId === Auth::id()) {
			return '#28a745'; //user yang sedang login
		}
		return '#dc3545'; //user lain
	}
}