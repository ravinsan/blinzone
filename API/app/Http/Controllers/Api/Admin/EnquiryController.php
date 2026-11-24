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
        $query = Enquiry::with('service:id,name', 'unit:id,name');

        if (!empty($request->search)) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        return $this->successResponse(
            $query->latest('id')->paginate($request->per_page ?? 10),
            'Enquiry list fetched successfully.'
        );
    }

    public function show($id)
    {
        $enquiry = Enquiry::with('service:id,name', 'unit:id,name')->find($id);

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
            'email'           => 'required|email|max:255',
            'address'         => 'required|string',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'pincode'         => 'required|string|max:20'
        ]);
        $data['preferred_date'] = $request->preferred_date ? date('Y-m-d', strtotime($request->preferred_date)) : null  ;
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
        $data['preferred_date'] = $request->preferred_date ? date('Y-m-d', strtotime($request->preferred_date)) : null  ;
        $enquiry->update($data);

        return $this->successResponse(
            $enquiry->fresh(),
            'Enquiry updated successfully.'
        );
    }
}