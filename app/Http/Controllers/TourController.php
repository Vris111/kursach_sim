<?php

namespace App\Http\Controllers;

use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function show(Tour $tour)
    {
        return new TourResource($tour);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'starting_date' => 'required|date',
            'days_count' => 'required|numeric|min:1',
            'peoples_count' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tour = Tour::create([
            'name' => $request->input('name'),
            'country' => $request->input('country'),
            'description' => $request->input('description'),
            'starting_date' => $request->input('starting_date'),
            'days_count' => $request->input('days_count'),
            'peoples_count' => $request->input('peoples_count'),
            'price' => $request->input('price'),
        ]);

        if ($request->hasFile('img')) {
            $img = $request->file('img');
            $imgName = time() . '.' . $img->getClientOriginalExtension();
            $img->storeAs('public/tour_images', $imgName);
            $tour->img = $imgName;
            $tour->save();
        }

        return response()->json([
            'message' => 'Tour was successfully created',
            'data' => new TourResource($tour)
        ], 201);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'starting_date' => 'required|date',
            'days_count' => 'required|numeric|min:1',
            'peoples_count' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tour->update([
            'name' => $request->input('name') ?? $tour->name,
            'country' => $request->input('country') ?? $tour->country,
            'description' => $request->input('description') ?? $tour->description,
            'starting_date' => $request->input('starting_date') ?? $tour->starting_date,
            'days_count' => $request->input('days_count') ?? $tour->days_count,
            'peoples_count' => $request->input('peoples_count') ?? $tour->peoples_count,
            'price' => $request->input('price') ?? $tour->price,
        ]);

        if ($request->hasFile('img')) {
            $img = $request->file('img');
            $imgName = time() . '.' . $img->getClientOriginalExtension();
            $img->storeAs('public/tour_images', $imgName);
            $tour->img = $imgName;
            $tour->save();
        }

        return response()->json([
            'message' => 'Tour was successfully updated',
            'data' => new TourResource($tour)
        ], 200);
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
