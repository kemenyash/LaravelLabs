<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has(['license_plate', 'brand'])) {
            $car = Car::where('license_plate', $request->license_plate)
                        ->where('brand', $request->brand)
                        ->first();

            if ($car) {
                return response()->json([
                    'status' => 200,
                    'data' => $car
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Car not found'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 200,
                'data' => Car::all()
            ]);
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $input['code'] = Car::count() + 1;

        $validatedData = $request->validate([
            'owner' => 'required|string|max:40',
            'brand' => 'required|string|max:40',
            'license_plate' => 'required|string|max:8|unique:cars',
            'color' => 'required|string|max:40',
        ]);

        $car = Car::create($validatedData);
        return response()->json([
            'status' => 201,
            'message' => 'Car added successfully'
        ], 201);
    }

    public function update(Request $request, $code)
    {
        $car = Car::find($code);

        if (!$car) {
            return response()->json([
                'status' => 404,
                'message' => 'Car not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'owner' => 'sometimes|required|string|max:40',
            'brand' => 'sometimes|required|string|max:40',
            'license_plate' => 'sometimes|required|string|max:8',
            'color' => 'sometimes|required|string|max:40',
        ]);

        $car->update($validatedData);
        return response()->json([
            'status' => 200,
            'message' => 'Car updated successfully'
        ]);
    }
}
