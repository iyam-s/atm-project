<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $nomorRekening = 'ATM' . rand(10000000, 99999999);

        $rekening = Rekening::create([
            'user_id' => $user->id,
            'nomor_rekening' => $nomorRekening,
            'saldo' => 0,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'rekening' => $rekening
        ], 201);
    }

    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Data tidak valid',
            'errors' => $validator->errors()
        ], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Email atau password salah'
        ], 401);
    }

    // Membuat token Sanctum
    $token = $user->createToken('atm-token')->plainTextToken;

    return response()->json([
        'message' => 'Login berhasil',
        'token' => $token,
        'user' => $user,
        'rekening' => $user->rekening
    ]);
    
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logout berhasil'
    ]);
}

public function gantiPin(Request $request)
{
    $validator = Validator::make($request->all(), [
        'pin_lama' => 'required',
        'pin_baru' => 'required|digits:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Data tidak valid',
            'errors' => $validator->errors()
        ], 422);
    }

    $user = $request->user();

    if (!Hash::check($request->pin_lama, $user->password)) {
        return response()->json([
            'message' => 'PIN lama salah'
        ], 401);
    }

    $user->password = Hash::make($request->pin_baru);
    $user->save();

    // Hapus semua token lama
    $user->tokens()->delete();

    return response()->json([
        'message' => 'PIN berhasil diganti. Silakan login kembali.'
    ]);
}
public function profile(Request $request)
{
    $user = $request->user()->load('rekening');

    return response()->json([
        'message' => 'Data profile berhasil diambil',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'rekening' => $user->rekening
    ]);
}

}