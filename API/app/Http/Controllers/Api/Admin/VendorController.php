<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Vendor::with(['user:id,name,email', 'addresses', 'bankDetails'])
                ->latest('id');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('business_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            /** @var \Illuminate\Pagination\LengthAwarePaginator $vendors */
            $vendors = $query->paginate($request->get('per_page', 10));

            $vendors->getCollection()->transform(function ($vendor) {
                if ($vendor->logo) {
                    $vendor->logo = url('storage/' . $vendor->logo);
                }

                return $vendor;
            });

            return $this->successResponse($vendors, 'Vendor list fetched successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor List Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'business_name' => 'required|string|max:255',
                'owner_name' => 'required|string|max:255',
                'phone' => 'required|string|max:30',
                'email' => 'required|email|max:255',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'    
            ]);

            $password = 12345678;
            $data = [
                'user_id'=> Auth::user()->id,
                'business_name' => $request->business_name,
                'owner_name' => $request->owner_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'text-password' => $password,
                'description' => $request->description,
                'offers' => $request->offers,
                'total_distance_cover' => $request->total_distance_cover,
                'delivery_free_km' => $request->delivery_free_km,
                'delivery_charge_per_km' => $request->delivery_charge_per_km,
                'delivery_time' => $request->delivery_time,
                'status' => $request->has('status') ? $request->status : true,
            ];

            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('vendors', 'public');
            }

            $vendor = Vendor::create($data);

            return $this->successResponse($vendor, 'Vendor created successfully.', 201);
        } catch (\Throwable $e) {
            Log::error('Vendor Create Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function show($id)
    {
        $vendor = Vendor::with(['user:id,name,email', 'addresses', 'bankDetails'])->find($id);

        if (!$vendor) {
            return $this->errorResponse('Vendor not found.', 404);
        }

        if ($vendor->logo) {
            $vendor->logo = url('storage/' . $vendor->logo);
        }

        return $this->successResponse($vendor, 'Vendor fetched successfully.');
    }

    public function update(Request $request, $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor) {
                return $this->errorResponse('Vendor not found.', 404);
            }

            $request->validate([
                'business_name' => 'nullable|string|max:255',
                'owner_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255',
                'status' => 'nullable|boolean',
            ]);

            $data = [];

            if ($request->has('business_name')) {
                $data['business_name'] = $request->business_name;
            }
            if ($request->has('owner_name')) {
                $data['owner_name'] = $request->owner_name;
            }
            if ($request->has('phone')) {
                $data['phone'] = $request->phone;
            }
            if ($request->has('email')) {
                $data['email'] = $request->email;
            }
            if ($request->has('description')) {
                $data['description'] = $request->description;
            }
            if ($request->has('offers')) {
                $data['offers'] = $request->offers;
            }
            if ($request->has('total_distance_cover')) {
                $data['total_distance_cover'] = $request->total_distance_cover;
            }
            if ($request->has('delivery_free_km')) {
                $data['delivery_free_km'] = $request->delivery_free_km;
            }
            if ($request->has('delivery_charge_per_km')) {
                $data['delivery_charge_per_km'] = $request->delivery_charge_per_km;
            }
            if ($request->has('delivery_time')) {
                $data['delivery_time'] = $request->delivery_time;
            }
            if ($request->has('status')) {
                $data['status'] = $request->status;
            }

            if ($request->hasFile('logo')) {
                if ($vendor->logo) {
                    Storage::disk('public')->delete($vendor->logo);
                }

                $data['logo'] = $request->file('logo')->store('vendors', 'public');
            }

            $vendor->update($data);

            return $this->successResponse($vendor->fresh(), 'Vendor updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Update Error', [
                'vendor_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor) {
                return $this->errorResponse('Vendor not found.', 404);
            }

            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }

            $vendor->addresses()->delete();
            $vendor->bankDetails()->delete();

            $vendor->delete();

            return $this->successResponse(null, 'Vendor deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Delete Error', [
                'vendor_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor) {
                return $this->errorResponse('Vendor not found.', 404);
            }

            $vendor->update([
                'status' => $vendor->status == 1 ? 0 : 1,
            ]);

            return $this->successResponse($vendor->fresh(), 'Vendor status updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Status Error', [
                'vendor_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }
}