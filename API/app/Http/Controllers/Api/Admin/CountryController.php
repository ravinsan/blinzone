<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        return $this->successResponse(
            $query->latest('id')->paginate($request->input('per_page', 10)),
            'Country list fetched successfully.'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'code'   => 'nullable|string|max:10',
            'status' => 'nullable|boolean',
        ]);

        $country = new Country();
        $country->name = $data['name'];
        $country->code = $data['code'] ?? null;
        $country->status = $data['status'] ?? true;
        $country->save();

        return $this->successResponse($country, 'Country created successfully.', 201);
    }

    public function show($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return $this->errorResponse('Country not found.', 404);
        }

        return $this->successResponse($country, 'Country fetched successfully.');
    }

    public function update(Request $request, $id)
    {
        $country = Country::find($id);

        if (!$country) {
            return $this->errorResponse('Country not found.', 404);
        }

        $data = $request->validate([
            'name'   => 'sometimes|required|string|max:100',
            'code'   => 'nullable|string|max:10',
            'status' => 'sometimes|boolean',
        ]);

        foreach ($data as $key => $value) {
            $country->{$key} = $value;
        }

        $country->save();

        return $this->successResponse($country->fresh(), 'Country updated successfully.');
    }

    public function destroy($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return $this->errorResponse('Country not found.', 404);
        }

        $country->delete();

        return $this->successResponse(null, 'Country deleted successfully.');
    }
}