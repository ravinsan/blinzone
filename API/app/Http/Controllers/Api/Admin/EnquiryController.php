<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\EnquiryImage;
use App\Models\Enquiry;


class EnquiryController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search'   => 'nullable|string|max:255',
            'status'   => 'nullable|string|max:50',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Enquiry::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $this->successResponse(
            $query->latest('id')->paginate($filters['per_page'] ?? 10),
            'Enquiry list fetched successfully.'
        );
    }

    public function show($id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return $this->errorResponse('Enquiry not found.', 404);
        }

        return $this->successResponse($enquiry, 'Enquiry fetched successfully.');
    }

    public function destroy($id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return $this->errorResponse('Enquiry not found.', 404);
        }

        $enquiry->delete();

        return $this->successResponse(null, 'Enquiry deleted successfully.');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'service_id'      => 'nullable|integer|exists:services,id',
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:30',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:20'
        ]);

        $enquiry = Enquiry::create($data);

        return $this->successResponse(
            $enquiry,
            'Enquiry created successfully.',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::find($id);

        if (!$enquiry) {
            return $this->errorResponse('Enquiry not found.', 404);
        }

        $data = $request->all();
        $request->validate([
            'service_id'      => 'sometimes|nullable|integer|exists:services,id',
            'name'            => 'sometimes|required|string|max:255',
            'phone'           => 'sometimes|required|string|max:30',
            'email'           => 'sometimes|nullable|email|max:255',
            'address'         => 'sometimes|nullable|string',
            'city'            => 'sometimes|nullable|string|max:100',
            'state'           => 'sometimes|nullable|string|max:100',
            'pincode'         => 'sometimes|nullable|string|max:20',
            'status'          => 'sometimes|string|max:50'
        ]);

        $enquiry->update($data);

        return $this->successResponse(
            $enquiry->fresh(),
            'Enquiry updated successfully.'
        );
    }
}