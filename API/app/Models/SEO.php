<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SEO extends Model
{
    use HasFactory;
    protected $fillable = [
        'page_id',
        'page_type',
        'meta_title',
        'meta_keyword',
        'meta_description'
    ];
}
