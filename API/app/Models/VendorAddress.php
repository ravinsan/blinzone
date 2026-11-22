<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'address_1',
        'address_2',
        'sector',
        'landmark',
        'country_id',
        'state_id',
        'city_id',
        'delivery_zone_id',
        'shop_image',
        'pan_card_image',
        'aadhaar_front_image',
        'aadhaar_back_image',
        'gst_number',
        'pan_number',
        'alternate_phone',
        'business_email',
        'website',
        'description',
        'latitude',
        'longitude',
        'fssai_number',
        'fssai_expiry_date',
        'status'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function deliveryZone()
    {
        return $this->belongsTo(DeliveryZone::class);
    }
    
}