<?php

namespace App\Http\Controllers;

use App\Models\ForgotPassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'ایمیل وارد شده صحیح نیست',
            ], 422);
        }
        $token = bin2hex(random_bytes(32));
        $expiresAt=now()->addMinutes(15);
        ForgotPassword::create([
            'email' => $user->email,
            'token' => $token,
            'expired_at' => $expiresAt,
        ]);

        $resetLink = "https://your-frontend.com/reset-password?token={$token}&email={$user->email}";

        Mail::raw(
            "سلام\n\n" .
            "برای بازنشانی رمز عبور خود روی لینک زیر کلیک کنید:\n" .
            "$resetLink\n\n" .
            "این لینک تا تاریخ زیر معتبر است:\n" .
            $expiresAt->toDateTimeString() . "\n\n" .
            "اگر این درخواست توسط شما انجام نشده است، این ایمیل را نادیده بگیرید.",
            function($message) use ($user) {
                $message->to($user->email)
                    ->subject('بازنشانی رمز عبور');
            }
        );


        return response()->json([
            'message' => 'لینک بازنشانی رمز به ایمیل شما ارسال شد.'
        ]);

    }

}
