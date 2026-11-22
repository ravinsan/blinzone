<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class StateController extends Controller
{
    use ApiResponseTrait;

    public function index($country_id)
    {
        if (!Country::find($country_id)) {
            return $this->errorResponse('Country not found.', 404);
        }

        $states = State::where('country_id', $country_id)
            ->orderBy('name')
            ->get();

        return $this->successResponse($states, 'State list fetched successfully.');
    }

    public function store(Request $request, $country_id)
    {
        if (!Country::find($country_id)) {
            return $this->errorResponse('Country not found.', 404);
        }

        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'code'   => 'nullable|string|max:20',
            'status' => 'nullable|boolean',
        ]);

        $state = new State();
        $state->country_id = $country_id;
        $state->name = $data['name'];
        $state->code = $data['code'] ?? null;
        $state->status = $data['status'] ?? true;
        $state->save();

        return $this->successResponse($state, 'State created successfully.', 201);
    }

    public function show($id, $country_id)
    {
        $state = State::where('country_id', $country_id)->find($id);

        if (!$state) {
            return $this->errorResponse('State not found for this country.', 404);
        }

        return $this->successResponse($state, 'State fetched successfully.');
    }

    public function update(Request $request, $id, $country_id)
    {
        $state = State::where('country_id', $country_id)->find($id);

        if (!$state) {
            return $this->errorResponse('State not found for this country.', 404);
        }

        $data = $request->validate([
            'name'   => 'sometimes|required|string|max:100',
            'code'   => 'nullable|string|max:20',
            'status' => 'sometimes|boolean',
        ]);

        foreach ($data as $key => $value) {
            $state->{$key} = $value;
        }

        $state->save();

        return $this->successResponse($state->fresh(), 'State updated successfully.');
    }

    public function destroy($id, $country_id)
    {
        $state = State::where('country_id', $country_id)->find($id);

        if (!$state) {
            return $this->errorResponse('State not found for this country.', 404);
        }

        $state->delete();

        return $this->successResponse(null, 'State deleted successfully.');
    }
}