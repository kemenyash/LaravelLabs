<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Purifier; 

class CarController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has(['license_plate', 'brand'])) {
            $car = Car::where('license_plate', strip_tags($request->license_plate)) 
                        ->where('brand', strip_tags($request->brand)) 
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
        $this->validateInput($request->all());

        $validatedData = $request->validate([
            'owner' => 'required|string|max:40',
            'brand' => 'required|string|max:40',
            'license_plate' => 'required|string|max:8|unique:cars',
            'color' => 'required|string|max:40',
        ]);

        
        $validatedData['creator_user_id'] = auth()->id();
        $validatedData['owner'] = strip_tags($validatedData['owner']);
        $validatedData['brand'] = strip_tags($validatedData['brand']);
        $validatedData['license_plate'] = strip_tags($validatedData['license_plate']);
        $validatedData['color'] = strip_tags($validatedData['color']);

        $car = Car::create($validatedData);
        return response()->json([
            'status' => 201,
            'message' => 'Car added successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'status' => 404,
                'message' => 'Car not found'
            ], 404);
        }

        $this->validateInput($request->all());

        $validatedData = $request->validate([
            'owner' => 'sometimes|required|string|max:40',
            'brand' => 'sometimes|required|string|max:40',
            'license_plate' => 'sometimes|required|string|max:8',
            'color' => 'sometimes|required|string|max:40',
        ]);

      
        $validatedData['owner'] = strip_tags($validatedData['owner']);
        $validatedData['brand'] = strip_tags($validatedData['brand']);
        $validatedData['license_plate'] = strip_tags($validatedData['license_plate']);
        $validatedData['color'] = strip_tags($validatedData['color']);

        $car->update($validatedData);
        return response()->json([
            'status' => 200,
            'message' => 'Car updated successfully'
        ]);
    }

    private function validateInput($input)
    {
        $brandRegex = '/^[A-Za-z0-9 ]{1,40}$/'; 
        $licensePlateRegex = '/^[A-Za-z0-9]{1,8}$/'; 
        $colorRegex = '/^[A-Za-z ]{1,40}$/'; 
        $ownerRegex = '/^[A-Za-z ]{1,40}$/'; 

        if (!isset($input['brand'], $input['license_plate'], $input['owner'], $input['color'])
            || !preg_match($brandRegex, $input['brand'])
            || !preg_match($licensePlateRegex, $input['license_plate'])
            || !preg_match($colorRegex, $input['color'])
            || !preg_match($ownerRegex, $input['owner'])) {
            return response()->json(['error' => 'Invalid input data'], 400)->send();
        }
    }
}
