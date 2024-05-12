<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())->get();
        return BookingResource::collection($bookings);
    }

    public function all_index()
    {
        $bookings = Booking::all();
        return BookingResource::collection($bookings);
    }

    public function store(Request $request, Tour $tour)
    {
        $validatedData = $request->validate([
            'tour_id' => 'required'
        ]);

        $tour = Tour::find($validatedData['tour_id']);
        if (!$tour) {
            return response()->json(['error' => 'Tour not found'], 404);
        }

        $booking = new Booking([
            'user_id' => auth()->id(),
            'tour_id' => $validatedData['tour_id'],
        ]);
        $booking->save();
        return $booking;
    }

    public function delete(Booking $booking)
    {
        $booking->delete();
        return response()->json(['message' =>'Booking was successfully deleted'], 200);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:отклонена,ожидание,одобрена'
        ],[
            'status.required' => 'Статус обязателен для заполнения',
            'status.in' => 'Необходимо корректное значение: отклонена,ожидание,одобрена',
        ]);
        if (!in_array($validatedData['status'], ['отклонена', 'ожидание', 'одобрена'])) {
            return response()->json(['error' => 'Неверное значение статуса'], 400);
        }
        $booking->status = $validatedData['status'];
        $booking->save();
        if ($validatedData['status'] == 'одобрена') {
            return response()->json([
                'message' => 'Booking status updated successfully. Please come to the office for confirmation, payment and document pickup.'
            ], 200);
        }
        return response()->json(['message' => 'Booking status updated successfully'], 200);
    }
}
