<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryImage extends Model
{
    use HasFactory;

    protected $fillable = ['enquiry_id', 'image'];
}
