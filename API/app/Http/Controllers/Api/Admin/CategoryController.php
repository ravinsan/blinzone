<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Category List
     */

    public function index(Request $request)
    {
        try {

            $query = Category::with('parent:id,name,slug,image');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Parent category filter
            if ($request->filled('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            }

            /** @var LengthAwarePaginator $categories */
            $categories = $query
                ->latest('id')
                ->paginate($request->get('per_page', 10));

            $categories->setCollection(
                $categories->getCollection()->map(function ($category) {

                    // Category image
                    if ($category->image) {
                        $category->image = url('storage/' . $category->image);
                    }

                    // Parent category image
                    if ($category->parent && $category->parent->image) {
                        $category->parent->image = url(
                            'storage/' . $category->parent->image
                        );
                    }

                    return $category;
                })
            );

            return $this->successResponse(
                $categories,
                'Category list fetched successfully.'
            );

        } catch (\Throwable $e) {

            Log::error('Category List Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function subCategories($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse('Category not found.', 404);
        }

        $subCategories = $category->subcategory()
            ->latest('id')
            ->get();

        $subCategories->transform(function ($subcategory) {
            if ($subcategory->image) {
                $subcategory->image = url('storage/' . $subcategory->image);
            }

            return $subcategory;
        });

        return $this->successResponse(
            $subCategories,
            'Subcategories fetched successfully.'
        );
    }

    /**
     * Create Category
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'name'        => 'required|string|max:255',
                'parent_id'   => 'nullable|exists:categories,id',
                'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'description' => 'nullable|string',
                'status'      => 'nullable|boolean',
            ]);

            $data = [
                'name'        => $request->name,
                'parent_id'   => $request->parent_id,
                'description' => $request->description,
                'status'      => $request->has('status')
                    ? $request->status
                    : true,
                'created_by'  => $request->user()->id,
                'updated_by'  => $request->user()->id,
            ];

            // Image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')
                    ->store('categories', 'public');
            }

            $category = Category::create($data);

            Log::info('Category Created', [
                'category_id' => $category->id,
                'created_by'  => $request->user()->id,
            ]);

            return $this->successResponse(
                $category,
                'Category created successfully.',
                201
            );

        } catch (\Throwable $e) {

            Log::error('Category Create Error', [
                'user_id' => optional($request->user())->id,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            throw $e;
        }
    }


    /**
     * Category Details
     */
    public function show($id)
    {
        $category = Category::with([
            'parent',
            'subcategory'
        ])->find($id);

        if (!$category) {
            return $this->errorResponse(
                'Category not found.',
                404
            );
        }

        return $this->successResponse(
            $category,
            'Category fetched successfully.'
        );
    }


    /**
     * Update Category
     */
    public function update(Request $request, $id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse(
                    'Category not found.',
                    404
                );
            }

            $request->validate([
                'name'        => 'required|string|max:255',
                'parent_id'   => 'nullable|exists:categories,id',
                'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'description' => 'nullable|string',
                'status'      => 'nullable|boolean',
            ]);

            $data = [
                'name'        => $request->name,
                'parent_id'   => $request->parent_id,
                'description' => $request->description,
                'updated_by'  => $request->user()->id,
            ];

            if ($request->has('status')) {
                $data['status'] = $request->status;
            }

            // New image
            if ($request->hasFile('image')) {

                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }

                $data['image'] = $request->file('image')
                    ->store('categories', 'public');
            }

            $category->update($data);

            Log::info('Category Updated', [
                'category_id' => $category->id,
                'updated_by'  => $request->user()->id,
            ]);

            return $this->successResponse(
                $category->fresh(),
                'Category updated successfully.'
            );

        } catch (\Throwable $e) {

            Log::error('Category Update Error', [
                'category_id' => $id,
                'user_id'     => optional($request->user())->id,
                'message'     => $e->getMessage(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
            ]);

            throw $e;
        }
    }


    /**
     * Delete Category
     */
    public function destroy(Request $request, $id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse(
                    'Category not found.',
                    404
                );
            }

            // Delete image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            // Delete subcategories if required
            $category->subcategory()->delete();

            $category->delete();

            Log::info('Category Deleted', [
                'category_id' => $id,
                'deleted_by'  => $request->user()->id,
            ]);

            return $this->successResponse(
                null,
                'Category deleted successfully.'
            );

        } catch (\Throwable $e) {

            Log::error('Category Delete Error', [
                'category_id' => $id,
                'user_id'     => optional($request->user())->id,
                'message'     => $e->getMessage(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
            ]);

            throw $e;
        }
    }


    /**
     * Change Category Status
     */
    public function changeStatus(Request $request, $id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse(
                    'Category not found.',
                    404
                );
            }
           
            $category->update([
                'status'     => ($category->status == 1) ? 0 : 1,
                'updated_by' => $request->user()->id,
            ]);

            Log::info('Category Status Changed', [
                'category_id' => $category->id,
                'status'      => $request->status,
                'updated_by'  => $request->user()->id,
            ]);

            return $this->successResponse(
                $category->fresh(),
                'Category status updated successfully.'
            );

        } catch (\Throwable $e) {

            Log::error('Category Status Error', [
                'category_id' => $id,
                'user_id'     => optional($request->user())->id,
                'message'     => $e->getMessage(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
            ]);

            throw $e;
        }
    }
}