<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponseTrait;
use App\Models\Slider;

class SliderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Slider List
     */
    public function index(Request $request)
    {
        try {
            $sliders = Slider::latest()->get();

            $sliders->transform(function ($slider) {

                if ($slider->image) {
                    $image = str_replace('public/', '', $slider->image);

                    $slider->image = url('storage/' . $image);
                }

                return $slider;
            });

            return $this->successResponse(
                $sliders,
                'Sliders fetched successfully.'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store Slider
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'title'       => 'required|string|max:255',
                'slider_type' => 'required',
                'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $slider = new Slider();

            $slider->title = $request->title;
            $slider->slider_type = $request->slider_type;
            $slider->status = 1;

            if ($request->hasFile('image')) {
                $slider->image = $request->file('image')
                    ->store('sliders', 'public');
            }

            if (auth()->check()) {
                $slider->created_by = auth()->id();
                $slider->updated_by = auth()->id();
            }

            $slider->save();

            return $this->successResponse(
                $slider,
                'Slider created successfully.',
                201
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Slider Details
     */
    public function show($id)
    {
        try {

            $slider = Slider::find($id);

            if (!$slider) {
                return $this->errorResponse(
                    'Slider not found.',
                    404
                );
            }

            if ($slider->image) {
                $image = str_replace('public/', '', $slider->image);

                $slider->image = url('storage/' . $image);
            }

            return $this->successResponse(
                $slider,
                'Slider fetched successfully.'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update Slider
     */
    public function update(Request $request, $id)
    {
        try {

            $slider = Slider::find($id);

            if (!$slider) {
                return $this->errorResponse(
                    'Slider not found.',
                    404
                );
            }

            $request->validate([
                'title'       => 'required|string|max:255',
                'slider_type' => 'required',
                'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'status'      => 'nullable|boolean',
            ]);

            $slider->title = $request->title;
            $slider->slider_type = $request->slider_type;

            if ($request->has('status')) {
                $slider->status = $request->status;
            }

            if ($request->hasFile('image')) {

                // Delete old image
                if (
                    $slider->image &&
                    Storage::disk('public')->exists($slider->image)
                ) {
                    Storage::disk('public')->delete($slider->image);
                }

                $slider->image = $request->file('image')
                    ->store('sliders', 'public');
            }

            if (auth()->check()) {
                $slider->updated_by = auth()->id();
            }

            $slider->save();

            return $this->successResponse(
                $slider,
                'Slider updated successfully.'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Delete Slider
     */
    public function destroy($id)
    {
        try {

            $slider = Slider::find($id);

            if (!$slider) {
                return $this->errorResponse(
                    'Slider not found.',
                    404
                );
            }

            // Delete image
            if (
                $slider->image &&
                Storage::disk('public')->exists($slider->image)
            ) {
                Storage::disk('public')->delete($slider->image);
            }

            $slider->delete();

            return $this->successResponse(
                null,
                'Slider deleted successfully.'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Change Slider Status
     */
    public function changeStatus($id)
    {
        try {

            $slider = Slider::find($id);

            if (!$slider) {
                return $this->errorResponse(
                    'Slider not found.',
                    404
                );
            }

            $slider->status = !$slider->status;

            if (auth()->check()) {
                $slider->updated_by = auth()->id();
            }

            $slider->save();

            return $this->successResponse(
                $slider,
                'Slider status changed successfully.'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }
}