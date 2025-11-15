<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function verifyEmail(Request $request, User $user): JsonResponse
    {
        if ($user->email_verified_at) {
            return response()->json(['message' => 'ایمیل قبلاً تایید شده است']);
        }
        $user->email_verified_at = now();
        $user->save();
        return response()->json(['message' => 'ایمیل شما با موفقیت تایید شد']);

    }
}
