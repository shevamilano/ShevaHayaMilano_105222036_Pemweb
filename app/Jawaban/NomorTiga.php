<?php

namespace App\Jawaban;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Log;

class NomorTiga
{
    public function getData()
    {
        try {
            
            return Event::where('user_id', Auth::id())
                ->get();
        } catch (\Exception $e) {
            Log::error('Data gagal diambil: ' . $e->getMessage());
            return collect(); 
        }
    }

    public function getSelectedData(Request $request)
    {
        try {
            Log::info('Memproses permintaan getSelectedData dengan data:', $request->all());

            $validatedData = $request->validate([
                'id' => 'required|integer|exists:events,id'
            ]);

            $event = Event::where('id', $validatedData['id'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$event) {
                Log::warning('Data jadwal tidak ditemukan');
                return response()->json([
                    'status' => false,
                    'message' => 'Jadwal tidak ditemukan atau Anda tidak memiliki akses'
                ], 400);
            }

            Log::info('Mengirim jadwal:', [
                'id' => $event->id,
                'name' => $event->name,
                'start' => $event->start->format('Y-m-d'),
                'end' => $event->end->format('Y-m-d')
            ]);

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'start' => $event->start->format('Y-m-d'),
                    'end' => $event->end->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data jadwal: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            Log::info('Memproses permintaan update:', $request->all());

            $validatedData = $request->validate([
                'id' => 'required|integer|exists:events,id',
                'name' => 'required|string',
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start'
            ]);

            $event = Event::where('id', $validatedData['id'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jadwal tidak ditemukan atau Anda tidak memiliki akses'
                ], 400);
            }

            $event->update([
                'name' => $validatedData['name'],
                'start' => $validatedData['start'],
                'end' => $validatedData['end']
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Jadwal berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui jadwal: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

	public function delete(Request $request)
	{
		try {
			$request->validate([
				'id' => 'required|exists:events,id',
			]);

			$event = Event::where('id', $request->id)
				->where('user_id', Auth::id())
				->firstOrFail();
			$event->delete();

			return response()->json([
				'status' => true,
				'message' => 'Jadwal berhasil dihapus',
			]);
		} catch (ModelNotFoundException $e) {
			return response()->json([
				'status' => false,
				'message' => 'Jadwal tidak ditemukan atau Anda tidak memiliki akses',
			], 404);
		} catch (Exception $e) {
			Log::error('Gagal menghapus jadwal: ' . $e->getMessage());
			return response()->json([
				'status' => false,
				'message' => 'Terjadi kesalahan saat menghapus jadwal',
			], 500);
		}
	}
}