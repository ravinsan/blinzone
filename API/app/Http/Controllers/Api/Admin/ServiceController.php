<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Service;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search'       => 'nullable|string|max:255',
            'vendor_id'    => 'nullable|integer|exists:vendors,id',
            'category_id'  => 'nullable|integer|exists:categories,id',
            'status'       => 'nullable|boolean',
            'is_verified'  => 'nullable|boolean',
            'per_page'     => 'nullable|integer|min:1|max:100',
        ]);

        $query = Service::with([
            'vendor:id,business_name',
            'category:id,name,slug',
            'subcategory:id,name,slug',
            'unit:id,name,short_name',
        ]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        foreach (['vendor_id', 'category_id', 'status', 'is_verified'] as $field) {
            if (array_key_exists($field, $filters)) {
                $query->where($field, $filters[$field]);
            }
        }

        return $this->successResponse(
            $query->latest('id')->paginate($filters['per_page'] ?? 10),
            'Service list fetched successfully.'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id'          => 'required|integer|exists:vendors,id',
            'category_id'        => 'required|integer|exists:categories,id',
            'unit_id'            => 'nullable|integer|exists:units,id',
            'name'               => 'required|string|max:255',
            'slug'               => 'nullable|string|max:255|alpha_dash',
            'short_description'  => 'nullable|string|max:500',
            'description'        => 'nullable|string',
            'image'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pincode'            => 'nullable|string',
        ]);

        unset($data['image']);

        $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $data['name']);
        $data['subcategory_id'] = $request->subcategory_id ?? 0;
        $data['price'] = $request->price ?? 0;
        $data['max_price'] = $request->max_price ?? 0;
        $data['experience'] = $request->experience ?? null;
        $data['service_area'] = $request->service_area ?? null;
        $data['pincode'] = $request->pincode ?? null;
        $data['attributes'] = $request->attributes ?? null;
        $data['status'] = $request->status ?? true;
        $data['is_verified'] = $request->is_verified ?? false;
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service = Service::create($data);

        return $this->successResponse(
            $service->load(['vendor', 'category', 'subcategory', 'unit']),
            'Service created successfully.',
            201
        );
    }

    public function show($id)
    {
        $service = Service::with(['vendor:id,owner_name,business_name', 'category:id,name', 'subcategory:id,name', 'unit:id,name'])->find($id);

        if (!$service) {
            return $this->errorResponse('Service not found.', 404);
        }

        if ($service->image) {
            $service->image = url('storage/' . $service->image);
        }

        return $this->successResponse($service, 'Service fetched successfully.');
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->errorResponse('Service not found.', 404);
        }

        $data = $request->validate([
            'vendor_id'          => 'sometimes|required|integer|exists:vendors,id',
            'category_id'        => 'sometimes|required|integer|exists:categories,id',
            'unit_id'            => 'sometimes|nullable|integer|exists:units,id',
            'name'               => 'sometimes|required|string|max:255',
            'slug'               => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('services', 'slug')->ignore($service->id),
            ],
            'short_description'  => 'sometimes|nullable|string|max:500',
            'description'        => 'sometimes|nullable|string',
            'image'              => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'             => 'sometimes|boolean'
        ]);

        unset($data['image']);

        if (array_key_exists('slug', $request->all()) || array_key_exists('name', $data)) {
            $slugSource = $data['slug'] ?? $data['name'] ?? $service->name;
            $data['slug'] = $this->makeUniqueSlug($slugSource, $service->id);
        }

        $data['updated_by'] = $request->user()->id;

        $oldImage = $service->image;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        if (isset($data['image']) && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return $this->successResponse(
            $service->fresh()->load(['vendor', 'category', 'subcategory', 'unit']),
            'Service updated successfully.'
        );
    }

    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->errorResponse('Service not found.', 404);
        }

        $image = $service->image;
        $service->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return $this->successResponse(null, 'Service deleted successfully.');
    }

    public function changeStatus($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->errorResponse('Service not found.', 404);
        }

        $service->update([
            'status' => !$service->status,
        ]);

        return $this->successResponse(
            $service->fresh(),
            'Service status updated successfully.'
        );
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'service';
        $slug = $base;
        $suffix = 2;

        while (
            Service::where('slug', $slug)
                ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }
}