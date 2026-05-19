<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\DiseaseScan;
use App\Models\ForumPost;
use App\Models\Sop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, true)) {
            $user = Auth::user();

            // Pastikan hanya pakar/admin yang bisa login ke CMS
            if (!in_array($user->role, ['pakar', 'admin'])) {
                Auth::logout();
                return back()->with('error', 'Akses ditolak. CMS hanya untuk Pakar dan Admin.');
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function index()
    {
        $stats = [
            'articles'      => Article::published()->count(),
            'articles_draft' => Article::where('is_published', false)->count(),
            'sops'          => Sop::published()->count(),
            'forum_total'   => ForumPost::approved()->count(),
            'forum_pending' => ForumPost::where('status', 'pending')->count(),
            'forum_unanswered' => ForumPost::approved()->where('is_answered', false)->count(),
            'scans_total'   => DiseaseScan::count(),
            'scans_today'   => DiseaseScan::whereDate('scanned_at', today())->count(),
            'users_petani'  => User::where('role', 'petani')->count(),
        ];

        // 5 post forum terbaru yang perlu perhatian
        $pendingPosts = ForumPost::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Sebaran penyakit terbanyak (top 5)
        $topDiseases = DiseaseScan::selectRaw('result_label_id, COUNT(*) as total')
            ->whereNotNull('result_label_id')
            ->groupBy('result_label_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Scan per komoditas
        $scanByCommodity = DiseaseScan::selectRaw('commodity, COUNT(*) as total')
            ->groupBy('commodity')
            ->get()
            ->pluck('total', 'commodity');

        return view('admin.dashboard', compact(
            'stats',
            'pendingPosts',
            'topDiseases',
            'scanByCommodity'
        ));
    }
}
