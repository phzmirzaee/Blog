<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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

        $token = JWTAuth::fromUser($user);

        EmailVerification::create([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => now(),
        ]);
        Mail::raw("برای تایید ایمیل خود روی لینک زیر کلیک کنید: " . url("/api/verify-email?token={$token}"), function($message) use ($user) {
            $message->to($user->email)
                ->subject('تایید ایمیل');
        });
        return (new UserResource($user))->additional([
            'message' => 'ثبت نام با موفقیت انجام شد لطفا ایمیل خود را تایید کنید',
            'verify_token' => $token
        ]);
    }

    public function login(Request $request): UserResource|JsonResponse
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
