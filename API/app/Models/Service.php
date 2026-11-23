<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'subcategory_id',
        'unit_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'max_price',
        'image',
        'experience',
        'service_area',
        'pincode',
        'attributes',
        'status',
        'is_verified',
        'sort_order',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'attributes' => 'array',
        'status' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class);
    }
   
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}