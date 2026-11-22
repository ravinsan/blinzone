<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponseTrait;
    
    /** * Login */ 
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email', 
                'password' => 'required',
                ]);
            $user = User::where('email', $request->email)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('API Login Failed', ['email' => $request->email, 'ip' => $request->ip(),]);
                return $this->errorResponse('Invalid email or password.', 401);
            }
            $token = $user->createToken('api-token')->plainTextToken;
            Log::info('API Login Successful', ['user_id' => $user->id, 'email' => $user->email, 'ip' => $request->ip(),]);
            return $this->successResponse(['token' => $token, 'user' => $user,], 'Login successful');
        } catch (\Throwable $e) {
            Log::error('API Login Exception', ['email' => $request->email, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(),]);
            throw $e;
        }
    }
    
    /** * Profile */ 
    public function profile(Request $request)
    {
        Log::info('API Profile Accessed', ['user_id' => Auth::user()->id]);
        return $this->successResponse(Auth::user(), 'Profile fetched successfully.');
    }
    
    /** * Logout */ 
    public function logout(Request $request)
{
    try {

        $user = $request->user();

        if (!$user) {
            return $this->errorResponse(
                'Unauthorized. Please login first.',
                401
            );
        }

        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        Log::info('API Logout Successful', [
            'user_id' => $user->id,
            'ip'      => $request->ip(),
        ]);

        return $this->successResponse(
            null,
            'Logout successful.'
        );

    } catch (\Throwable $e) {

        Log::error('API Logout Exception', [
            'user_id' => optional($request->user())->id,
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);

        throw $e;
    }
}
}
