@extends('layouts.app')
@section('title', $sop ? 'Edit SOP' : 'Buat SOP Baru')

@section('content')

    <div class="max-w-4xl">
        <form method="POST" action="{{ $sop ? route('admin.sops.update', $sop->id) : route('admin.sops.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if ($sop)
                @method('PUT')
            @endif

            {{-- INFO DASAR --}}
            <div class="card space-y-5 mb-6">
                <h2 class="font-semibold text-gray-800 text-lg border-b pb-3">Informasi Dasar</h2>

                <div>
                    <label class="form-label">Judul SOP <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $sop?->title) }}"
                        class="form-input @error('title') border-red-500 @enderror"
                        placeholder="Contoh: SOP Budidaya Cabai Merah - Dataran Rendah Malang">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Komoditas <span class="text-red-500">*</span></label>
                        <select name="commodity" class="form-input">
                            @foreach (['cabai' => '🌶️ Cabai', 'kentang' => '🥔 Kentang', 'jagung' => '🌽 Jagung'] as $v => $l)
                                <option value="{{ $v }}" @selected(old('commodity', $sop?->commodity) === $v)>
                                    {{ $l }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Durasi (hari)</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', $sop?->duration_days) }}"
                            class="form-input" placeholder="Contoh: 120" min="1">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <div class="flex items-center h-10">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $sop?->is_published))
                                    class="w-4 h-4 text-primary-600 rounded">
                                <span class="text-sm text-gray-700">Terbitkan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="form-label">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" class="form-input"
                        placeholder="Gambaran umum SOP ini untuk siapa dan kondisi apa...">{{ old('description', $sop?->description) }}</textarea>
                </div>

                <div>
                    <label class="form-label">Gambar Sampul</label>
                    @if ($sop?->thumbnail)
                        <img src="{{ asset('storage/' . $sop->thumbnail) }}"
                            class="h-28 w-auto rounded-lg mb-2 object-cover" alt="">
                        <p class="text-xs text-gray-400 mb-2">Upload baru untuk mengganti.</p>
                    @endif
                    <input type="file" name="thumbnail" accept="image/*" class="form-input">
                </div>
            </div>

            {{-- KALENDER AKTIVITAS --}}
            <div class="card mb-6">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="font-semibold text-gray-800 text-lg">Kalender Aktivitas Bulanan</h2>
                    <button type="button" onclick="addRow()" class="btn-primary text-sm">+ Tambah Aktivitas</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="calendarTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-th w-16">Bulan</th>
                                <th class="table-th w-16">Minggu</th>
                                <th class="table-th w-48">Aktivitas</th>
                                <th class="table-th">Detail / Keterangan</th>
                                <th class="table-th w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                            {{-- Diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Hidden input yang akan diisi JSON --}}
                <input type="hidden" name="monthly_calendar" id="monthly_calendar">
                @error('monthly_calendar')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- INPUT YANG DIBUTUHKAN --}}
            <div class="card mb-6">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="font-semibold text-gray-800 text-lg">Kebutuhan Input (Pupuk/Pestisida)</h2>
                    <button type="button" onclick="addInput()" class="btn-primary text-sm">+ Tambah Input</button>
                </div>

                <div id="inputsContainer" class="space-y-3"></div>
                <input type="hidden" name="inputs_needed" id="inputs_needed">
            </div>

            <div class="flex gap-3">
                <button type="submit" onclick="serializeData()" class="btn-primary">
                    {{ $sop ? 'Simpan Perubahan' : 'Simpan SOP' }}
                </button>
                <a href="{{ route('admin.sops.index') }}" class="btn-secondary">Batal</a>
            </div>

        </form>
    </div>

    @push('scripts')
        <script>
            // Data awal (saat edit)
            let calendarData = @json($sop?->monthly_calendar ?? []);
            let inputsData = @json($sop?->inputs_needed ?? []);

            // ===== KALENDER =====
            function renderCalendar() {
                const tbody = document.getElementById('calendarBody');
                tbody.innerHTML = '';
                calendarData.forEach((row, idx) => {
                    tbody.innerHTML += `
        <tr class="border-t border-gray-100" id="cal-row-${idx}">
            <td class="px-3 py-2">
                <input type="number" min="1" max="12" value="${row.month || ''}"
                       onchange="calendarData[${idx}].month=parseInt(this.value)"
                       class="w-16 form-input text-center py-1 px-2 text-sm">
            </td>
            <td class="px-3 py-2">
                <input type="number" min="1" max="4" value="${row.week || ''}"
                       onchange="calendarData[${idx}].week=parseInt(this.value)"
                       class="w-16 form-input text-center py-1 px-2 text-sm">
            </td>
            <td class="px-3 py-2">
                <input type="text" value="${row.activity || ''}"
                       onchange="calendarData[${idx}].activity=this.value"
                       placeholder="Nama aktivitas..."
                       class="form-input py-1 px-2 text-sm">
            </td>
            <td class="px-3 py-2">
                <textarea onchange="calendarData[${idx}].details=this.value"
                          placeholder="Detail teknis..."
                          rows="2"
                          class="form-input py-1 px-2 text-sm w-full resize-none">${row.details || ''}</textarea>
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeRow(${idx})"
                        class="text-red-400 hover:text-red-600 text-lg leading-none">×</button>
            </td>
        </tr>`;
                });
            }

            function addRow() {
                const lastMonth = calendarData.length > 0 ?
                    calendarData[calendarData.length - 1].month || 1 :
                    1;
                calendarData.push({
                    month: lastMonth,
                    week: 1,
                    activity: '',
                    details: ''
                });
                renderCalendar();
            }

            function removeRow(idx) {
                calendarData.splice(idx, 1);
                renderCalendar();
            }

            // ===== INPUTS NEEDED =====
            function renderInputs() {
                const container = document.getElementById('inputsContainer');
                container.innerHTML = '';
                if (inputsData.length === 0) {
                    container.innerHTML =
                        '<p class="text-sm text-gray-400 text-center py-4">Belum ada input. Klik tombol di atas untuk menambah.</p>';
                    return;
                }
                inputsData.forEach((inp, idx) => {
                    container.innerHTML += `
        <div class="flex gap-3 items-start bg-gray-50 rounded-lg p-3" id="inp-row-${idx}">
            <div class="flex-1">
                <input type="text" value="${inp.name || ''}"
                       onchange="inputsData[${idx}].name=this.value"
                       placeholder="Nama input (contoh: Urea)"
                       class="form-input text-sm mb-2">
            </div>
            <div class="flex-1">
                <input type="text" value="${inp.dose || ''}"
                       onchange="inputsData[${idx}].dose=this.value"
                       placeholder="Dosis (contoh: 200 kg/ha)"
                       class="form-input text-sm mb-2">
            </div>
            <div class="flex-1">
                <input type="text" value="${inp.timing || ''}"
                       onchange="inputsData[${idx}].timing=this.value"
                       placeholder="Waktu aplikasi"
                       class="form-input text-sm mb-2">
            </div>
            <button type="button" onclick="removeInput(${idx})"
                    class="text-red-400 hover:text-red-600 text-xl leading-none mt-2">×</button>
        </div>`;
                });
            }

            function addInput() {
                inputsData.push({
                    name: '',
                    dose: '',
                    timing: ''
                });
                renderInputs();
            }

            function removeInput(idx) {
                inputsData.splice(idx, 1);
                renderInputs();
            }

            // ===== SERIALIZE — Dipanggil saat submit =====
            function serializeData() {
                // Baca semua nilai terkini dari input fields sebelum serialize
                document.getElementById('monthly_calendar').value = JSON.stringify(calendarData);
                document.getElementById('inputs_needed').value = JSON.stringify(inputsData);
            }

            // Init render saat load
            renderCalendar();
            renderInputs();
        </script>
    @endpush

@endsection
