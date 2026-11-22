<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = DeliveryZone::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('city_name', 'like', '%' . $search . '%')
                    ->orWhere('pincode', 'like', '%' . $search . '%');
            });
        }

        return $this->successResponse(
            $query->latest('id')->paginate($request->input('per_page', 10)),
            'Delivery zone list fetched successfully.'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'city_name' => 'required|string|max:255',
            'pincode'   => 'required|integer',
        ]);

        $zone = DeliveryZone::create($data);

        return $this->successResponse($zone, 'Delivery zone created successfully.', 201);
    }

    public function show($id)
    {
        $zone = DeliveryZone::find($id);

        if (!$zone) {
            return $this->errorResponse('Delivery zone not found.', 404);
        }

        return $this->successResponse($zone, 'Delivery zone fetched successfully.');
    }

    public function update(Request $request, $id)
    {
        $zone = DeliveryZone::find($id);

        if (!$zone) {
            return $this->errorResponse('Delivery zone not found.', 404);
        }

        $data = $request->validate([
            'city_name' => 'sometimes|required|string|max:255',
            'pincode'   => 'sometimes|required|integer',
        ]);

        $zone->update($data);

        return $this->successResponse($zone->fresh(), 'Delivery zone updated successfully.');
    }

    public function destroy($id)
    {
        $zone = DeliveryZone::find($id);

        if (!$zone) {
            return $this->errorResponse('Delivery zone not found.', 404);
        }

        $zone->delete();

        return $this->successResponse(null, 'Delivery zone deleted successfully.');
    }
}