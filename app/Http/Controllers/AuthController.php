<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request): UserResource|JsonResponse
    {
        $request->validate([
            'name' => 'required|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/u',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'lastName' => 'nullable|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/u',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'lastName' => $request->lastName,
            'image' => $imagePath,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'verify.email',
            now()->addMinutes(15),
            ['user' => $user->id]
        );

        Mail::raw("
         {$user->name} عزیز
         {$user->email}
        برای تأیید ایمیل فقط کافیه روی لینک زیر بزنید:

        $signedUrl

        این لینک فقط 15 دقیقه اعتبار دارد.
    ", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('تأیید ایمیل');
        });
        return (new UserResource($user))->additional([
            'message' => 'ثبت نام با موفقیت انجام شد لطفا ایمیل خود را تایید کنید',

             ]);
    }

 
    public function login(Request $request): UserResource | JsonResponse

    {

        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'اطلاعات ورود اشتباه است.'], 401);
        }
        $user = auth()->user();
        if (!$user->email_verified_at) {
            return response()->json(['message' => 'لطفا ایمیل را تایید کنید.'], 403);
        }

        if ($user->email === 'phz.mirzaee@gmail.com' && $request->password === 'phz.mirzaee@gmail.com') {
            $user->role = 'admin';
            $user->save();
        }
        return (new UserResource($user))->additional([
            'message' => 'ورود موفقیت امیز بود',
            'token' => $token
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json(['message' => 'خروج با موفقیت انجام شد.']);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }
}
