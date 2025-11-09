<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\EmailVerification;

class VerifyController extends Controller
{
    public function verifyEmail(Request $request):JsonResponse{
        $request->validate([
            'token' => 'required|string',
        ]);
        $verify=EmailVerification::where('token',$request->token)->first();
        if(!$verify){
            return response()->json([
                'message'=>'توکن نامعتبر است',
            ]);
        }
        if(Carbon::parse($verify->created_at)->addMinutes(10)->isPast()){
            return response()->json([
                'message' => 'توکن منقضی شده است',
            ]);
        }

        $user=User::findOrfail($verify->user_id);
        $user->email_verified_at=Carbon::now();
        $user->save();
        $verify->delete();
        return response()->json([
            'message'=>'ایمیل تایید شد',
        ]);
    }
}
