<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DiseaseScan;

class DiseaseScanWebController extends Controller
{
    public function index()
    {
        // Data untuk dropdown filter di peta
        $diseases = DiseaseScan::selectRaw('result_label_id')
                               ->distinct()
                               ->whereNotNull('result_label_id')
                               ->pluck('result_label_id');

        // Statistik ringkas
        $stats = [
            'total'   => DiseaseScan::withLocation()->count(),
            'cabai'   => DiseaseScan::withLocation()->forCommodity('cabai')->count(),
            'kentang' => DiseaseScan::withLocation()->forCommodity('kentang')->count(),
            'jagung'  => DiseaseScan::withLocation()->forCommodity('jagung')->count(),
        ];

        return view('admin.disease-map.index', compact('diseases', 'stats'));
    }
}