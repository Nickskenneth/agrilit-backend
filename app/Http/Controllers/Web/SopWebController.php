<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SopWebController extends Controller
{
    public function index()
    {
        $sops = Sop::with('author')->orderBy('commodity')->get();
        return view('admin.sops.index', compact('sops'));
    }

    public function create()
    {
        return view('admin.sops.form', ['sop' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'commodity'   => 'required|in:cabai,kentang,jagung',
            'description' => 'nullable|string',
            'duration_days' => 'nullable|integer|min:1',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // monthly_calendar dikirim sebagai JSON string dari form
            'monthly_calendar' => 'required|string',
            'inputs_needed'    => 'nullable|string',
        ]);

        // Validasi JSON bisa di-parse
        $calendar = json_decode($validated['monthly_calendar'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['monthly_calendar' => 'Format kalender tidak valid.'])->withInput();
        }

        $inputsNeeded = null;
        if (!empty($validated['inputs_needed'])) {
            $inputsNeeded = json_decode($validated['inputs_needed'], true);
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('sops', 'public');
        }

        Sop::create([
            'author_id'        => Auth::id(),
            'title'            => $validated['title'],
            'slug'             => Str::slug($validated['title']) . '-' . uniqid(),
            'commodity'        => $validated['commodity'],
            'description'      => $validated['description'] ?? null,
            'duration_days'    => $validated['duration_days'] ?? null,
            'monthly_calendar' => $calendar,
            'inputs_needed'    => $inputsNeeded,
            'thumbnail'        => $thumbnailPath,
            'is_published'     => $request->has('is_published'),
        ]);

        return redirect()->route('admin.sops.index')
            ->with('success', 'SOP berhasil disimpan.');
    }

    public function edit(string $id)
    {
        $sop = Sop::findOrFail($id);
        return view('admin.sops.form', compact('sop'));
    }

    public function update(Request $request, string $id)
    {
        $sop = Sop::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'commodity'        => 'required|in:cabai,kentang,jagung',
            'description'      => 'nullable|string',
            'duration_days'    => 'nullable|integer|min:1',
            'thumbnail'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'monthly_calendar' => 'required|string',
            'inputs_needed'    => 'nullable|string',
        ]);

        $calendar = json_decode($validated['monthly_calendar'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['monthly_calendar' => 'Format kalender tidak valid.'])->withInput();
        }

        $thumbnailPath = $sop->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumbnailPath) \Storage::disk('public')->delete($thumbnailPath);
            $thumbnailPath = $request->file('thumbnail')->store('sops', 'public');
        }

        $sop->update([
            'title'            => $validated['title'],
            'commodity'        => $validated['commodity'],
            'description'      => $validated['description'] ?? null,
            'duration_days'    => $validated['duration_days'] ?? null,
            'monthly_calendar' => $calendar,
            'inputs_needed'    => !empty($validated['inputs_needed'])
                ? json_decode($validated['inputs_needed'], true)
                : null,
            'thumbnail'        => $thumbnailPath,
            'is_published'     => $request->has('is_published'),
        ]);

        return redirect()->route('admin.sops.index')
            ->with('success', 'SOP berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $sop = Sop::findOrFail($id);
        if ($sop->thumbnail) \Storage::disk('public')->delete($sop->thumbnail);
        $sop->delete();
        return back()->with('success', 'SOP berhasil dihapus.');
    }
}
