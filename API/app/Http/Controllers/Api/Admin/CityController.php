<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use ApiResponseTrait;

    public function index($state_id)
    {
        if (!State::find($state_id)) {
            return $this->errorResponse('State not found.', 404);
        }

        $cities = City::where('state_id', $state_id)
            ->orderBy('name')
            ->get();

        return $this->successResponse($cities, 'City list fetched successfully.');
    }

    public function store(Request $request, $state_id)
    {
        if (!State::find($state_id)) {
            return $this->errorResponse('State not found.', 404);
        }

        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'status' => 'nullable|boolean',
        ]);

        $city = new City();
        $city->state_id = $state_id;
        $city->name = $data['name'];
        $city->status = $data['status'] ?? true;
        $city->save();

        return $this->successResponse($city, 'City created successfully.', 201);
    }

    public function show($id, $state_id)
    {
        $city = City::where('state_id', $state_id)->find($id);

        if (!$city) {
            return $this->errorResponse('City not found for this state.', 404);
        }

        return $this->successResponse($city, 'City fetched successfully.');
    }

    public function update(Request $request, $id, $state_id)
    {
        $city = City::where('state_id', $state_id)->find($id);

        if (!$city) {
            return $this->errorResponse('City not found for this state.', 404);
        }

        $data = $request->validate([
            'name'   => 'sometimes|required|string|max:100',
            'status' => 'sometimes|boolean',
        ]);

        foreach ($data as $key => $value) {
            $city->{$key} = $value;
        }

        $city->save();

        return $this->successResponse($city->fresh(), 'City updated successfully.');
    }

    public function destroy($id, $state_id)
    {
        $city = City::where('state_id', $state_id)->find($id);

        if (!$city) {
            return $this->errorResponse('City not found for this state.', 404);
        }

        $city->delete();

        return $this->successResponse(null, 'City deleted successfully.');
    }
}