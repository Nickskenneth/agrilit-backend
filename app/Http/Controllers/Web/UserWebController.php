<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserWebController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('role')->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(20)->withQueryString();

        $counts = [
            'admin'  => User::where('role', 'admin')->count(),
            'pakar'  => User::where('role', 'pakar')->count(),
            'petani' => User::where('role', 'petani')->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:petani,pakar,admin',
            'phone'    => 'nullable|string|max:20',
            'region'   => 'nullable|string|max:255',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'phone'    => $validated['phone'] ?? null,
            'region'   => $validated['region'] ?? null,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function updateRole(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Mencegah admin mengubah role dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa mengubah role akun sendiri.');
        }

        $request->validate(['role' => 'required|in:petani,pakar,admin']);
        $user->update(['role' => $request->role]);

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$request->role}.");
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
