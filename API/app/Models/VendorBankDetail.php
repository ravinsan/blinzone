<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'ifsc_code',
        'upi_id',
        'qr_code_image'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}