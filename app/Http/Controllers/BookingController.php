<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        $validator = Validator::make($request->all(), [
            'tour_name' => 'required|string|max:255',
        ],[
            'tour_name.required' => 'Tour name is required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tour = Tour::where('name', $request->tour_name)->first();
        if (!$tour) {
            return response()->json(['message' => 'Tour not found'], 404);
        }

        $booking = new Booking([
            'user_id' => auth()->id(),
            'tour_id' => $tour->id,
        ]);
        $booking->save();
        return response()->json(['message' => 'Booking created successfully'], 200);
    }

    public function delete(Booking $booking)
    {
        $booking->delete();
        return response()->json(['message' =>'Booking was successfully deleted'], 200);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:отклонена,ожидание,одобрена'
        ],[
            'status.required' => 'The status is required',
            'status.in' => 'The correct value is required: отклонена,ожидание,одобрена',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $booking->status = $request->input('status');
        $booking->save();
        if ($booking->status == 'одобрена') {
            return response()->json([
                'message' => 'Booking status updated successfully. Please come to the office for confirmation, payment and document pickup.'
            ], 200);
        }
        return response()->json(['message' => 'Booking status updated successfully'], 200);
    }
}
