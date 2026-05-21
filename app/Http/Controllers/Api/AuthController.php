<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Masyarakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $masyarakat = Masyarakat::where('username', $request->username)->first();

        if (!$masyarakat || !Hash::check($request->password, $masyarakat->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 401);
        }

        $token = $masyarakat->createToken('android-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $masyarakat,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nik'      => 'required|digits:16|unique:masyarakat,nik',
            'nama'     => 'required|string|max:35',
            'username' => 'required|string|max:25|unique:masyarakat,username',
            'password' => 'required|string|min:6',
            'telp'     => 'required|string|max:13',
        ]);

        $masyarakat = Masyarakat::create([
            'nik'      => $request->nik,
            'nama'     => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'telp'     => $request->telp,
        ]);

        $token = $masyarakat->createToken('android-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $masyarakat,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }
}
