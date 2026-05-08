<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiseaseScan;
use App\Models\User;

class DiseaseScanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'petani')->get();

        // Koordinat area pertanian di wilayah Malang
        // Pujon, Ngantang, Tumpang, Kepanjen, Lawang
        $locations = [
            ['lat' => -7.8312, 'lng' => 112.5264, 'name' => 'Pujon, Malang'],
            ['lat' => -7.8456, 'lng' => 112.4892, 'name' => 'Ngantang, Malang'],
            ['lat' => -7.9234, 'lng' => 112.7821, 'name' => 'Tumpang, Malang'],
            ['lat' => -8.1234, 'lng' => 112.5678, 'name' => 'Kepanjen, Malang'],
            ['lat' => -7.7234, 'lng' => 112.6789, 'name' => 'Lawang, Malang'],
            ['lat' => -7.8901, 'lng' => 112.5234, 'name' => 'Pujon, Malang'],
            ['lat' => -7.9012, 'lng' => 112.6123, 'name' => 'Batu, Malang'],
        ];

        $scanData = [
            // Cabai
            [
                'commodity'       => 'cabai',
                'result_label'    => 'Pepper__bell___Bacterial_spot',
                'result_label_id' => 'Bercak Bakteri pada Cabai',
                'confidence_score' => 0.9234,
            ],
            [
                'commodity'       => 'cabai',
                'result_label'    => 'Pepper__bell___healthy',
                'result_label_id' => 'Cabai Sehat',
                'confidence_score' => 0.9876,
            ],
            [
                'commodity'       => 'cabai',
                'result_label'    => 'Pepper__bell___Bacterial_spot',
                'result_label_id' => 'Bercak Bakteri pada Cabai',
                'confidence_score' => 0.8712,
            ],
            // Kentang
            [
                'commodity'       => 'kentang',
                'result_label'    => 'Potato___Late_blight',
                'result_label_id' => 'Busuk Daun (Late Blight)',
                'confidence_score' => 0.9456,
            ],
            [
                'commodity'       => 'kentang',
                'result_label'    => 'Potato___Early_blight',
                'result_label_id' => 'Bercak Awal (Early Blight)',
                'confidence_score' => 0.8934,
            ],
            [
                'commodity'       => 'kentang',
                'result_label'    => 'Potato___Late_blight',
                'result_label_id' => 'Busuk Daun (Late Blight)',
                'confidence_score' => 0.9123,
            ],
            [
                'commodity'       => 'kentang',
                'result_label'    => 'Potato___healthy',
                'result_label_id' => 'Kentang Sehat',
                'confidence_score' => 0.9567,
            ],
            // Jagung
            [
                'commodity'       => 'jagung',
                'result_label'    => 'Corn_(maize)___Common_rust_',
                'result_label_id' => 'Karat Biasa pada Jagung',
                'confidence_score' => 0.9012,
            ],
            [
                'commodity'       => 'jagung',
                'result_label'    => 'Corn_(maize)___Northern_Leaf_Blight',
                'result_label_id' => 'Hawar Daun Utara',
                'confidence_score' => 0.8678,
            ],
            [
                'commodity'       => 'jagung',
                'result_label'    => 'Corn_(maize)___healthy',
                'result_label_id' => 'Jagung Sehat',
                'confidence_score' => 0.9789,
            ],
        ];

        foreach ($scanData as $index => $data) {
            $user     = $users[$index % $users->count()];
            $location = $locations[$index % count($locations)];

            // Tambahkan sedikit variasi koordinat agar titik tidak menumpuk persis
            $latVariasi = (rand(-500, 500) / 100000);
            $lngVariasi = (rand(-500, 500) / 100000);

            DiseaseScan::create([
                'user_id'         => $user->id,
                'image_path'      => 'scans/dummy/scan_' . ($index + 1) . '.jpg',
                'commodity'       => $data['commodity'],
                'result_label'    => $data['result_label'],
                'result_label_id' => $data['result_label_id'],
                'confidence_score' => $data['confidence_score'],
                'latitude'        => $location['lat'] + $latVariasi,
                'longitude'       => $location['lng'] + $lngVariasi,
                'location_name'   => $location['name'],
                'synced'          => true,
                'scanned_at'      => now()->subDays(rand(1, 30)),
                'notes'           => null,
            ]);
        }
    }
}