<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SopResource;
use App\Models\Sop;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SopController extends Controller
{
    use ApiResponseTrait;

    // ============================================================
    // INDEX — List semua SOP
    // GET /api/sops
    // Query params: ?commodity=cabai
    // ============================================================
    public function index(Request $request)
    {
        $query = Sop::published()
                    ->with('author')
                    ->orderBy('commodity');

        if ($request->filled('commodity')) {
            $query->forCommodity($request->commodity);
        }

        $sops = $query->get(); // SOP tidak perlu pagination, jumlahnya sedikit

        return $this->successResponse(
            SopResource::collection($sops),
            'Data SOP berhasil dimuat.'
        );
    }

    // ============================================================
    // SHOW — Detail satu SOP (termasuk full monthly_calendar)
    // GET /api/sops/{id}
    // ============================================================
    public function show(string $id)
    {
        $sop = Sop::published()
                  ->with('author')
                  ->findOrFail($id);

        return $this->successResponse(
            new SopResource($sop),
            'Detail SOP berhasil dimuat.'
        );
    }

    // ============================================================
    // SYNC — Metadata SOP untuk offline sync
    // GET /api/sops/sync
    // ============================================================
    public function sync()
    {
        $sops = Sop::published()
                   ->select('id', 'commodity', 'updated_at')
                   ->get()
                   ->map(fn($s) => [
                       'id'         => $s->id,
                       'commodity'  => $s->commodity,
                       'updated_at' => $s->updated_at->toISOString(),
                   ]);

        return $this->successResponse($sops, 'Sync metadata SOP berhasil.');
    }
}