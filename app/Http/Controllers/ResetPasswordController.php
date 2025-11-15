<?php

namespace App\Http\Controllers;

use App\Models\ForgotPassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
        ]);
        $reset = ForgotPassword::where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        if (!$reset) {
            return response()->json([
                'message' => 'ایمیل یا توکن اشتباه است',
            ], 404);
        }
        if ($reset->expired_at < now()) {
            $reset->delete();
            return response()->json([
                'message' => 'لینک منقضی شده است'
            ], 410);
        }
        $user = User::where('email', $reset->email)->first();
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'رمز جدید نباید با رمز قبلی یکسان باشد.'
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        $rows = ForgotPassword::where('email', $request->email)
            ->where('token', $request->token)
            ->delete();

        return response()->json([
            'message' => 'رمز با موفقیت تغییر کرد'
        ], 200);
    }
}
