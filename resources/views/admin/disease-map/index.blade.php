@extends('layouts.app')
@section('title', 'Peta Sebaran Penyakit')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach ([['label' => 'Total Titik', 'value' => $stats['total'], 'icon' => '📍'], ['label' => 'Cabai', 'value' => $stats['cabai'], 'icon' => '🌶️'], ['label' => 'Kentang', 'value' => $stats['kentang'], 'icon' => '🥔'], ['label' => 'Jagung', 'value' => $stats['jagung'], 'icon' => '🌽']] as $s)
            <div class="card flex items-center gap-3 py-3">
                <span class="text-2xl">{{ $s['icon'] }}</span>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $s['value'] }}</p>
                    <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="form-label">Filter Komoditas</label>
                <select id="filterCommodity" class="form-input">
                    <option value="">Semua Komoditas</option>
                    <option value="cabai">🌶️ Cabai</option>
                    <option value="kentang">🥔 Kentang</option>
                    <option value="jagung">🌽 Jagung</option>
                </select>
            </div>
            <div>
                <label class="form-label">Filter Penyakit</label>
                <select id="filterDisease" class="form-input">
                    <option value="">Semua Penyakit</option>
                    @foreach ($diseases as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <button onclick="applyFilter()" class="btn-primary">Terapkan Filter</button>
            <button onclick="resetFilter()" class="btn-secondary">Reset</button>
        </div>
    </div>

    {{-- Map --}}
    <div class="card p-0 overflow-hidden">
        <div id="map" style="height: 520px; width: 100%;"></div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            // Inisialisasi peta — center di Malang
            const map = L.map('map').setView([-7.9797, 112.6304], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Warna marker per komoditas
            const colors = {
                cabai: '#ef4444',
                kentang: '#eab308',
                jagung: '#f97316',
            };

            // Icon custom per warna
            function makeIcon(color) {
                return L.divIcon({
                    html: `<div style="
            background:${color};
            width:14px;height:14px;
            border-radius:50%;
            border:2px solid white;
            box-shadow:0 1px 3px rgba(0,0,0,0.4)
        "></div>`,
                    className: '',
                    iconSize: [14, 14],
                    iconAnchor: [7, 7],
                });
            }

            let markers = L.layerGroup().addTo(map);

            async function loadMapData(commodity = '', disease = '') {
                let url = '/admin/map-data?';
                if (commodity) url += `commodity=${commodity}&`;
                if (disease) url += `disease=${encodeURIComponent(disease)}&`;

                try {
                    // Ambil token dari meta (kita perlu token pakar untuk hit API)
                    const token = document.querySelector('meta[name="api-token"]')?.content;
                    const res = await fetch(url);
                    const json = await res.json();

                    markers.clearLayers();

                    if (!json.data || json.data.length === 0) {
                        return;
                    }

                    json.data.forEach(point => {
                        const color = colors[point.commodity] || '#6b7280';
                        const marker = L.marker([point.lat, point.lng], {
                            icon: makeIcon(color)
                        });

                        marker.bindPopup(`
                <div style="min-width:180px">
                    <p style="font-weight:600;font-size:13px;margin-bottom:4px">
                        ${point.result_label_id}
                    </p>
                    <p style="font-size:11px;color:#555;margin:2px 0">
                        📍 ${point.location_name || 'Tidak diketahui'}
                    </p>
                    <p style="font-size:11px;color:#555;margin:2px 0">
                        🌱 ${point.commodity} · ${point.confidence}
                    </p>
                    <p style="font-size:11px;color:#888;margin:2px 0">
                        🕐 ${new Date(point.scanned_at).toLocaleDateString('id-ID')}
                    </p>
                </div>
            `);

                        markers.addLayer(marker);
                    });

                } catch (err) {
                    console.error('Gagal load data peta:', err);
                }
            }

            function applyFilter() {
                const commodity = document.getElementById('filterCommodity').value;
                const disease = document.getElementById('filterDisease').value;
                loadMapData(commodity, disease);
            }

            function resetFilter() {
                document.getElementById('filterCommodity').value = '';
                document.getElementById('filterDisease').value = '';
                loadMapData();
            }

            // Load data saat halaman pertama dibuka
            loadMapData();
        </script>
    @endpush

@endsection
