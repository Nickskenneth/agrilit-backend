<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiseaseScanResource;
use App\Models\DiseaseScan;
use App\Services\PlantDiseaseService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiseaseScanController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private PlantDiseaseService $diseaseService
    ) {}

    // ============================================================
    // SCAN — Upload foto dan dapatkan hasil prediksi AI
    // POST /api/scans
    // Form-data:
    //   image     : file foto daun
    //   commodity : cabai | kentang | jagung
    //   latitude  : (opsional) koordinat GPS
    //   longitude : (opsional) koordinat GPS
    //   scanned_at: (opsional) timestamp jika foto diambil offline
    //   notes     : (opsional) catatan petani
    // ============================================================
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // max 5MB
            'commodity'  => 'required|in:cabai,kentang,jagung',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'scanned_at' => 'nullable|date',
            'notes'      => 'nullable|string|max:500',
        ]);

        // Simpan gambar ke storage
        $imagePath = $request->file('image')->store('scans', 'public');

        // Kirim ke FastAPI (atau mock saat development)
        $prediction = $this->diseaseService->predict($imagePath, $validated['commodity']);

        // Simpan hasil scan ke database
        $scan = DiseaseScan::create([
            'user_id'          => $request->user()->id,
            'image_path'       => $imagePath,
            'commodity'        => $validated['commodity'],
            'result_label'     => $prediction['result_label'],
            'result_label_id'  => $prediction['result_label_id'],
            'confidence_score' => $prediction['confidence_score'],
            'latitude'         => $validated['latitude'] ?? null,
            'longitude'        => $validated['longitude'] ?? null,
            'synced'           => true,
            'scanned_at'       => $validated['scanned_at'] ?? now(),
            'notes'            => $validated['notes'] ?? null,
        ]);

        $statusMessage = $prediction['success']
            ? 'Analisis berhasil.'
            : 'Analisis gagal, silakan coba lagi.';

        return $this->successResponse(
            new DiseaseScanResource($scan),
            $statusMessage,
            201
        );
    }

    // ============================================================
    // SYNC OFFLINE — Flutter kirim batch data scan yang tersimpan
    //                di SQLite saat offline
    // POST /api/scans/sync
    // Body: {"scans": [{...}, {...}]}
    // ============================================================
    public function syncOffline(Request $request)
    {
        $request->validate([
            'scans'                  => 'required|array|min:1|max:20',
            'scans.*.commodity'      => 'required|in:cabai,kentang,jagung',
            'scans.*.result_label'   => 'nullable|string',
            'scans.*.result_label_id'=> 'nullable|string',
            'scans.*.confidence_score'=> 'nullable|numeric',
            'scans.*.latitude'       => 'nullable|numeric',
            'scans.*.longitude'      => 'nullable|numeric',
            'scans.*.scanned_at'     => 'required|date',
            'scans.*.notes'          => 'nullable|string',
        ]);

        $saved  = 0;
        $failed = 0;

        DB::beginTransaction();
        try {
            foreach ($request->scans as $scanData) {
                DiseaseScan::create([
                    'user_id'          => $request->user()->id,
                    'image_path'       => 'scans/offline/no-image.jpg',
                    'commodity'        => $scanData['commodity'],
                    'result_label'     => $scanData['result_label'] ?? null,
                    'result_label_id'  => $scanData['result_label_id'] ?? null,
                    'confidence_score' => $scanData['confidence_score'] ?? null,
                    'latitude'         => $scanData['latitude'] ?? null,
                    'longitude'        => $scanData['longitude'] ?? null,
                    'synced'           => true,
                    'scanned_at'       => $scanData['scanned_at'],
                    'notes'            => $scanData['notes'] ?? null,
                ]);
                $saved++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Offline sync failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('Sinkronisasi gagal, coba lagi.', 500);
        }

        return $this->successResponse([
            'saved'  => $saved,
            'failed' => $failed,
        ], "Sinkronisasi berhasil: {$saved} data tersimpan.");
    }

    // ============================================================
    // HISTORY — Riwayat scan milik user yang login
    // GET /api/scans
    // Query params: ?commodity=cabai, ?per_page=10
    // ============================================================
    public function history(Request $request)
    {
        $query = DiseaseScan::where('user_id', $request->user()->id)
                            ->orderBy('scanned_at', 'desc');

        if ($request->filled('commodity')) {
            $query->forCommodity($request->commodity);
        }

        $scans = $query->paginate(min($request->get('per_page', 10), 50));

        return $this->paginatedResponse(
            $scans->through(fn($scan) => new DiseaseScanResource($scan))
        );
    }

    // ============================================================
    // MAP DATA — Data untuk peta sebaran penyakit (admin)
    // GET /api/scans/map
    // Query params: ?commodity=kentang, ?disease=Late_blight
    // Hanya mengembalikan data yang punya koordinat GPS
    // ============================================================
    public function mapData(Request $request)
    {
        $query = DiseaseScan::withLocation()
                            ->select([
                                'id', 'commodity', 'result_label',
                                'result_label_id', 'confidence_score',
                                'latitude', 'longitude',
                                'location_name', 'scanned_at',
                            ])
                            ->orderBy('scanned_at', 'desc');

        if ($request->filled('commodity')) {
            $query->forCommodity($request->commodity);
        }

        // Filter berdasarkan jenis penyakit spesifik
        if ($request->filled('disease')) {
            $query->where('result_label', 'like', '%' . $request->disease . '%');
        }

        // Batasi 500 titik agar peta tidak overload
        $scans = $query->limit(500)->get();

        // Format ringkas untuk peta — tidak perlu semua field
        $mapPoints = $scans->map(fn($scan) => [
            'id'             => $scan->id,
            'commodity'      => $scan->commodity,
            'result_label_id'=> $scan->result_label_id,
            'confidence'     => round($scan->confidence_score * 100, 1) . '%',
            'lat'            => (float) $scan->latitude,
            'lng'            => (float) $scan->longitude,
            'location_name'  => $scan->location_name,
            'scanned_at'     => $scan->scanned_at,
        ]);

        return $this->successResponse($mapPoints, 'Data peta berhasil dimuat.');
    }

    // ============================================================
    // SHOW — Detail satu hasil scan
    // GET /api/scans/{id}
    // ============================================================
    public function show(Request $request, string $id)
    {
        $scan = DiseaseScan::where('user_id', $request->user()->id)
                           ->findOrFail($id);

        return $this->successResponse(new DiseaseScanResource($scan));
    }
}