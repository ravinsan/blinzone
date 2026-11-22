<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'url', 'parent_id', 'order_id', 'icon', 'status', 'created_by'];

    public function parent()
    {
    	return $this->belongsTo(Menu::class, 'parent_id')->select('id', 'name');
    }
}
