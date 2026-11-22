<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'owner_name',
        'phone',
        'email',
        'password',
        'text_password',
        'logo',
        'description',
        'offers',
        'total_distance_cover',
        'delivery_free_km',
        'delivery_charge_per_km',
        'delivery_time',
        'status'
    ];

    // Vendor belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->hasMany(VendorAddress::class);
    }

    public function bankDetails()
    {
        return $this->hasMany(VendorBankDetail::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}