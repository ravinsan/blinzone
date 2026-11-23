<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorAddress;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VendorAddressController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request, $vendor_id)
    {
        try {
            $query = VendorAddress::with(['vendor:id,business_name,owner_name,phone,email', 'country:id,name', 'state:id,name', 'city:id,name', 'deliveryZone:id,city_name,pincode'])
                ->latest('id');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('address_1', 'like', "%{$search}%")
                        ->orWhere('sector', 'like', "%{$search}%")
                        ->orWhere('business_email', 'like', "%{$search}%")
                        ->orWhere('gst_number', 'like', "%{$search}%")
                        ->orWhere('pan_number', 'like', "%{$search}%");
                });
            }

            $query->where('vendor_id', $vendor_id);
            
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            /** @var \Illuminate\Pagination\LengthAwarePaginator $addresses */
            $addresses = $query->paginate($request->get('per_page', 10));

            $addresses->getCollection()->transform(function ($address) {
                foreach (['shop_image', 'pan_card_image', 'aadhaar_front_image', 'aadhaar_back_image'] as $field) {
                    if ($address->{$field}) {
                        $address->{$field} = url('storage/' . $address->{$field});
                    }
                }

                return $address;
            });

            return $this->successResponse($addresses, 'Vendor address list fetched successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Address List Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function store(Request $request, $vendor_id)
    {
        try {
            $request->validate([
                'address_1' => 'required|string|max:255',
                'address_2' => 'nullable|string|max:255',
                'sector' => 'nullable|string|max:100',
                'landmark' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'delivery_zone_id' => 'required|exists:delivery_zones,id',
                'shop_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'pan_card_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'aadhaar_front_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'aadhaar_back_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
                'pan_number' => 'required|string|max:50'                
            ]);

            $data = [
                'vendor_id' => $vendor_id,
                'address_1' => $request->address_1,
                'address_2' => $request->address_2,
                'sector' => $request->sector,
                'landmark' => $request->landmark,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'delivery_zone_id' => $request->delivery_zone_id,
                'gst_number' => $request->gst_number,
                'pan_number' => $request->pan_number,
                'alternate_phone' => $request->alternate_phone,
                'business_email' => $request->business_email,
                'website' => $request->website,
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'fssai_number' => $request->fssai_number,
                'fssai_expiry_date' => $request->fssai_expiry_date ? date('Y-m-d', strtotime($request->fssai_expiry_date)) : null,
                'status' => $request->has('status') ? $request->status : true,
            ];

            foreach (['shop_image', 'pan_card_image', 'aadhaar_front_image', 'aadhaar_back_image'] as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $request->file($field)->store('vendor-addresses', 'public');
                }
            }

            $address = VendorAddress::create($data);

            return $this->successResponse($address, 'Vendor address created successfully.', 201);
        } catch (\Throwable $e) {
            Log::error('Vendor Address Create Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function show($id, $vendor_id)
    {
        $address = VendorAddress::with(['vendor:id,business_name,owner_name,phone,email', 'country:id,name', 'state:id,name', 'city:id,name', 'deliveryZone:id,city_name,pincode'])->where('id', $id)->where('vendor_id', $vendor_id)->first();

        if (!$address) {
            return $this->errorResponse('Vendor address not found.', 404);
        }

        foreach (['shop_image', 'pan_card_image', 'aadhaar_front_image', 'aadhaar_back_image'] as $field) {
            if ($address->{$field}) {
                $address->{$field} = url('storage/' . $address->{$field});
            }
        }

        return $this->successResponse($address, 'Vendor address fetched successfully.');
    }

    public function update(Request $request, $id, $vendor_id)
    {
        try {
            $address = VendorAddress::find($id);

            if (!$address) {
                return $this->errorResponse('Vendor address not found.', 404);
            }

            $request->validate([
                'address_1' => 'nullable|string|max:255',
                'address_2' => 'nullable|string|max:255',
                'sector' => 'nullable|string|max:100',
                'landmark' => 'nullable|string|max:255',
                'country_id' => 'nullable|exists:countries,id',
                'state_id' => 'nullable|exists:states,id',
                'city_id' => 'nullable|exists:cities,id',
                'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
                'pan_number' => 'nullable|string|max:50',
                'status' => 'nullable|boolean',
            ]);

            $data = [];

            foreach ([
                        'address_1', 'address_2', 'sector', 'landmark',
                        'country_id', 'state_id', 'city_id', 'delivery_zone_id',
                        'gst_number', 'pan_number', 'alternate_phone', 'business_email',
                        'website', 'description', 'latitude', 'longitude',
                        'fssai_number', 'fssai_expiry_date', 'status'
                    ] as $field) {
                        if ($request->has($field)) {
                            $data[$field] = $field === 'fssai_expiry_date'
                                ? ($request->filled($field)
                                    ? \Carbon\Carbon::createFromFormat('d-m-Y', $request->input($field))->format('Y-m-d')
                                    : null)
                                : $request->input($field);
                        }
                    }

            foreach (['shop_image', 'pan_card_image', 'aadhaar_front_image', 'aadhaar_back_image'] as $field) {
                if ($request->hasFile($field)) {
                    if ($address->{$field}) {
                        Storage::disk('public')->delete($address->{$field});
                    }
                    $data[$field] = $request->file($field)->store('vendor-addresses', 'public');
                }
            }

            $address->update($data);

            return $this->successResponse($address->fresh(), 'Vendor address updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Address Update Error', [
                'vendor_address_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function destroy(Request $request, $id, $vendor_id)
    {
        try {
            $address = VendorAddress::where('id', $id)->where('vendor_id', $vendor_id)->first();

            if (!$address) {
                return $this->errorResponse('Vendor address not found.', 404);
            }

            foreach (['shop_image', 'pan_card_image', 'aadhaar_front_image', 'aadhaar_back_image'] as $field) {
                if ($address->{$field}) {
                    Storage::disk('public')->delete($address->{$field});
                }
            }

            $address->delete();

            return $this->successResponse(null, 'Vendor address deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Address Delete Error', [
                'vendor_address_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }
}