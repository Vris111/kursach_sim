<?php

namespace App\Http\Controllers;

use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::all();
        return TourResource::collection($tours);
    }

    public function indexWeb()
    {
        $tours = Tour::all();
        return view('tours.index', compact('tours'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'starting_date' => 'required|date|before:ending_date',
            'ending_date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        if ($validatedData['price'] <= 0) {
            return response()->json(['error' => 'Price must be greater than zero'], 422);
        }

        $tour = Tour::create([
            'name' => $validatedData['name'],
            'country' => $validatedData['country'],
            'description' => $validatedData['description'],
            'starting_date' => $validatedData['starting_date'],
            'ending_date' => $validatedData['ending_date'],
            'price' => $validatedData['price'],
        ]);
        return new TourResource($tour);
    }

    public function delete(Tour $tour)
    {
        if (!$tour) {
            return response()->json(['error' => 'Tour not found'], 404);
        }
        $tour->delete();
        return response()->json(['message' =>'Tour was successfully deleted'], 200);
    }
    public function update(Request $request, Tour $tour)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'starting_date' => 'required|date|before:ending_date',
            'ending_date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        if ($validatedData['price'] <= 0) {
            return response()->json(['error' => 'Price must be greater than zero'], 422);
        }

        $tour->update([
            'name' => $validatedData['name'] ?? $tour->name,
            'country' => $validatedData['country'] ?? $tour->country,
            'description' => $validatedData['description'] ?? $tour->description,
            'starting_date' => $validatedData['starting_date'] ?? $tour->starting_date,
            'ending_date' => $validatedData['ending_date'] ?? $tour->ending_date,
            'price' => $validatedData['price'] ?? $tour->price,
        ]);
        return new TourResource($tour);
    }
    public function searchTours(Request $request)
    {
        $name = $request->input('name');
        $tours = Tour::where('name', 'like', "%{$name}%")->get();
        if ($tours->isEmpty()) {
            return response()->json(['message' => 'No tours found'], 200);
        }
        return TourResource::collection($tours);
    }
}
