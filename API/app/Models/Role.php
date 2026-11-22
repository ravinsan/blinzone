<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'guard_name', 'role_level', 'status', 'created_by'];

    // ------------- ADD Role HERE -------------------
    public static function addRoles($id,$data){
        return Role::updateOrCreate(['id'=>$id],$data);
    }
}
