<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorBankDetail;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VendorBankDetailController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request, $vendor_id)
    {
        try {
            $query = VendorBankDetail::with('vendor:id,owner_name,business_name,phone,email')->where('vendor_id', $vendor_id)->latest('id');

            if ($request->filled('vendor_id')) {
                $query->where('vendor_id', $request->vendor_id);
            }

            /** @var \Illuminate\Pagination\LengthAwarePaginator $bankDetails */
            $bankDetails = $query->paginate($request->get('per_page', 10));

            $bankDetails->getCollection()->transform(function ($detail) {
                if ($detail->qr_code_image) {
                    $detail->qr_code_image = url('storage/' . $detail->qr_code_image);
                }

                return $detail;
            });

            return $this->successResponse($bankDetails, 'Vendor bank detail list fetched successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Bank Detail List Error', [
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
                'bank_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:100',
                'account_holder_name' => 'required|string|max:255',
                'ifsc_code' => 'required|string|max:20'
            ]);



            $data = [
                'vendor_id' => $vendor_id,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'ifsc_code' => $request->ifsc_code,
                'upi_id' => $request->upi_id,
            ];

            if ($request->hasFile('qr_code_image')) {
                $data['qr_code_image'] = $request->file('qr_code_image')->store('vendor-bank-details', 'public');
            }

            $bankDetail = VendorBankDetail::create($data);

            return $this->successResponse($bankDetail, 'Vendor bank detail created successfully.', 201);
        } catch (\Throwable $e) {
            Log::error('Vendor Bank Detail Create Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function show($id)
    {
        $bankDetail = VendorBankDetail::with('vendor:id,owner_name,business_name,phone,email')->find($id);

        if (!$bankDetail) {
            return $this->errorResponse('Vendor bank detail not found.', 404);
        }

        if ($bankDetail->qr_code_image) {
            $bankDetail->qr_code_image = url('storage/' . $bankDetail->qr_code_image);
        }

        return $this->successResponse($bankDetail, 'Vendor bank detail fetched successfully.');
    }

    public function update(Request $request, $id, $vendor_id)
    {
        try {
            $bankDetail = VendorBankDetail::where('vendor_id', $vendor_id)->find($id);

            if (!$bankDetail) {
                return $this->errorResponse('Vendor bank detail not found.', 404);
            }

            $request->validate([
                'bank_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:100',
                'account_holder_name' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:20'
            ]);

            $data = [];

            foreach (['bank_name', 'account_number', 'account_holder_name', 'ifsc_code', 'upi_id'] as $field) {
                if ($request->has($field)) {
                    $data[$field] = $request->input($field);
                }
            }

            if ($request->hasFile('qr_code_image')) {
                if ($bankDetail->qr_code_image) {
                    Storage::disk('public')->delete($bankDetail->qr_code_image);
                }

                $data['qr_code_image'] = $request->file('qr_code_image')->store('vendor-bank-details', 'public');
            }

            $bankDetail->update($data);

            return $this->successResponse($bankDetail->fresh(), 'Vendor bank detail updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Bank Detail Update Error', [
                'vendor_bank_detail_id' => $id,
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
            $bankDetail = VendorBankDetail::where('vendor_id', $vendor_id)->find($id);

            if (!$bankDetail) {
                return $this->errorResponse('Vendor bank detail not found.', 404);
            }

            if ($bankDetail->qr_code_image) {
                Storage::disk('public')->delete($bankDetail->qr_code_image);
            }

            $bankDetail->delete();

            return $this->successResponse(null, 'Vendor bank detail deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Vendor Bank Detail Delete Error', [
                'vendor_bank_detail_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }
}