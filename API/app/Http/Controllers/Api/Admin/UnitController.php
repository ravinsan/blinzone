<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        // Fetch units from the database
        $units = Unit::where('status', 1)->get();

        return response()->json([
            'success' => true,
            'data' => $units,
            'message' => 'Units fetched successfully.'
        ]);
    }
}
