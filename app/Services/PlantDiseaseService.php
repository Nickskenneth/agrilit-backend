<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlantDiseaseService
{
    private string $fastApiUrl;

    public function __construct()
    {
        // URL FastAPI — diambil dari .env
        // Saat local: http://localhost:8001
        // Saat production: https://ai.agrilit.id
        $this->fastApiUrl = config('services.fastapi.url', 'http://localhost:8001');
    }

    // ============================================================
    // PREDICT — Kirim gambar ke FastAPI, terima hasil prediksi
    // ============================================================
    public function predict(string $imagePath, string $commodity): array
    {
        if (config('services.fastapi.mock', true)) {
            return $this->mockResponse($commodity);
        }

        try {
            // PENTING: commodity ada di URL path → /predict/cabai
            $response = Http::timeout(30)
                ->attach(
                    'file',
                    file_get_contents(storage_path('app/public/' . $imagePath)),
                    'image.jpg'
                )
                ->post("{$this->fastApiUrl}/predict/{$commodity}");

            if ($response->successful()) {
                $data = $response->json();

                Log::info('FastAPI prediction result', [
                    'commodity_sent' => $commodity,
                    'prediction'     => $data['prediction'] ?? 'null',
                    'confidence'     => $data['confidence'] ?? 'null',
                ]);

                return [
                    'success'          => true,
                    'result_label'     => $data['prediction'],
                    'result_label_id'  => $data['prediction_id'],
                    'confidence_score' => $data['confidence_normalized'],
                ];
            }

            Log::warning('FastAPI error response', [
                'commodity_sent' => $commodity,
                'status'         => $response->status(),
                'body'           => $response->body(),
            ]);

            return $this->fallbackResponse();
        } catch (\Exception $e) {
            Log::error('FastAPI unreachable', [
                'commodity_sent' => $commodity,
                'error'          => $e->getMessage(),
            ]);
            return $this->fallbackResponse();
        }
    }

    // ============================================================
    // MOCK — Response palsu saat FastAPI belum tersedia
    // Digunakan selama development frontend & testing
    // ============================================================
    private function mockResponse(string $commodity): array
    {
        $mockData = [
            'cabai' => [
                ['label' => 'Pepper__bell___Bacterial_spot', 'label_id' => 'Bercak Bakteri pada Cabai', 'confidence' => 0.9234],
                ['label' => 'Pepper__bell___healthy',        'label_id' => 'Cabai Sehat',               'confidence' => 0.9876],
            ],
            'kentang' => [
                ['label' => 'Potato___Late_blight',  'label_id' => 'Busuk Daun (Late Blight)',    'confidence' => 0.9456],
                ['label' => 'Potato___Early_blight', 'label_id' => 'Bercak Awal (Early Blight)', 'confidence' => 0.8934],
            ],
            'jagung' => [
                ['label' => 'Corn_(maize)___Common_rust_',       'label_id' => 'Karat Biasa pada Jagung', 'confidence' => 0.9012],
                ['label' => 'Corn_(maize)___Northern_Leaf_Blight', 'label_id' => 'Hawar Daun Utara',       'confidence' => 0.8678],
                ['label' => 'Corn_(maize)___healthy',            'label_id' => 'Jagung Sehat',            'confidence' => 0.9789],
            ],
        ];

        $options = $mockData[$commodity] ?? $mockData['cabai'];
        $picked  = $options[array_rand($options)];

        return [
            'success'          => true,
            'result_label'     => $picked['label'],
            'result_label_id'  => $picked['label_id'],
            'confidence_score' => $picked['confidence'],
        ];
    }

    // ============================================================
    // FALLBACK — Jika FastAPI error di production
    // ============================================================
    private function fallbackResponse(): array
    {
        return [
            'success'          => false,
            'result_label'     => null,
            'result_label_id'  => 'Gagal dianalisis — coba lagi',
            'confidence_score' => null,
        ];
    }
}
