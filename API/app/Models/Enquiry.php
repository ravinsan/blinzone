<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'name', 'phone', 'email', 'description', 'work_type', 'budget_min', 'budget_max', 'area', 'unit_id', 'address', 'city', 'state', 'pincode', 'preferred_date', 'preferred_time', 'status'];
}
