<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ============================================================
    // REGISTER — Hanya untuk role petani
    // POST /api/auth/register
    // ============================================================
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // butuh password_confirmation
            'phone'    => 'nullable|string|max:20',
            'region'   => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'petani', // role selalu petani saat register publik
            'phone'    => $validated['phone'] ?? null,
            'region'   => $validated['region'] ?? null,
        ]);

        $token = $user->createToken('agrilit-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'token'   => $token,
            'user'    => $this->formatUser($user),
        ], 201);
    }

    // ============================================================
    // LOGIN
    // POST /api/auth/login
    // ============================================================
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Hapus token lama agar tidak menumpuk (opsional tapi direkomendasikan)
        $user->tokens()->delete();

        $token = $user->createToken('agrilit-mobile')->plainTextToken;

        // Update last login
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => $this->formatUser($user),
        ]);
    }

    // ============================================================
    // LOGOUT
    // POST /api/auth/logout
    // Header: Authorization: Bearer {token}
    // ============================================================
    public function logout(Request $request)
    {
        // Hapus hanya token yang sedang dipakai
        // Guard: currentAccessToken() bisa null saat auth via session (bukan Bearer token)
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    // ============================================================
    // ME — Data user yang sedang login
    // GET /api/auth/me
    // ============================================================
    public function me(Request $request)
    {
        return response()->json([
            'user' => $this->formatUser($request->user()),
        ]);
    }

    // ============================================================
    // HELPER — Format data user yang dikembalikan ke Flutter
    // Private karena hanya dipakai di controller ini
    // ============================================================
    private function formatUser(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $user->phone,
            'region'     => $user->region,
            'avatar_url' => $user->avatar
                            ? asset('storage/' . $user->avatar)
                            : null,
        ];
    }
}