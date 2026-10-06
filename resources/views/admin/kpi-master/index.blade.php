@extends('layouts.admin')

@section('title', 'KPI Master')

@section('content')
    @php
        $divisiNama = $divisiList->firstWhere('id', $divisiId)->nama ?? '-';
        $angka = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',');
    @endphp

    <div x-data="{
            open: false,
            mode: 'tambah',
            action: '',
            form: {},
            kosong: { id: '', key_result_area: '', nama_kpi: '', deskripsi: '', sumber_data: '', satuan: '%', pola: 'maximize', rumus_realisasi: '', rumus_target: '', bobot: '', target: '', target_fleksibel: false, is_active: true, dipakai: 0, jumlah_orang: 0 },
            tambah() {
                this.mode = 'tambah';
                this.action = '{{ route('admin.kpi-master.store') }}';
                this.form = Object.assign({}, this.kosong);
                this.open = true;
            },
            edit(k) {
                this.mode = 'edit';
                this.action = '{{ url('admin/kpi-master') }}/' + k.id;
                this.form = Object.assign({}, k);
                this.open = true;
            },
            satuanTarget() {
                return { '%': '%', 'Rupiah': 'Rp' }[this.form.satuan] || this.form.satuan;
            }
         }">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">KPI Master</h1>
                <p class="text-gray-500 mt-1">Daftar KPI per divisi, sama seperti dokumen KPI. Tambah, edit, atau hapus langsung dari tabel.</p>
            </div>
            <button type="button" @click="tambah()"
                    class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg text-sm whitespace-nowrap">
                + Tambah KPI
            </button>
        </div>

        @if (session('status'))
            <div class="mb-6 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
            <select name="divisi_id" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                @foreach ($divisiList as $divisi)
                    <option value="{{ $divisi->id }}" @selected($divisiId == $divisi->id)>{{ $divisi->nama }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-500">Periode target</label>
                <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
                       class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="nonaktif" value="1" @checked($tampilNonaktif) onchange="this.form.submit()">
                Tampilkan yang nonaktif
            </label>
        </form>

        <div class="bg-white rounded-2xl overflow-x-auto">
            <table class="w-full text-sm min-w-[1100px]">
                <thead>
                    <tr class="text-left text-gray-500 border-b bg-gray-50">
                        <th class="py-3 pl-4 pr-3 w-10">No.</th>
                        <th class="py-3 pr-4">Key Result Area</th>
                        <th class="py-3 pr-4">Indikator</th>
                        <th class="py-3 pr-4">Sumber Data</th>
                        <th class="py-3 pr-4">Satuan</th>
                        <th class="py-3 pr-4">Pola</th>
                        <th class="py-3 pr-4 text-right">Bobot</th>
                        <th class="py-3 pr-4">Rumus Perhitungan</th>
                        <th class="py-3 pr-4 text-right">Target</th>
                        <th class="py-3 pr-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $kraSebelumnya = null; @endphp
                    @forelse ($kpiList as $i => $kpi)
                        <tr class="border-b align-top {{ $kpi->is_active ? '' : 'opacity-50' }}">
                            <td class="py-3 pl-4 pr-3 text-gray-500">{{ $i + 1 }}</td>
                            <td class="py-3 pr-4 text-gray-700">
                                @if ($kpi->key_result_area !== $kraSebelumnya){{ $kpi->key_result_area }}@endif
                            </td>
                            <td class="py-3 pr-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $kpi->nama_kpi }}
                                    @if ($kpi->target_fleksibel)
                                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-normal bg-orange-50 text-orange-700">Fleksibel</span>
                                    @endif
                                    @unless ($kpi->is_active)
                                        <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-normal bg-red-50 text-red-700">Nonaktif</span>
                                    @endunless
                                </div>
                                @if ($kpi->deskripsi)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $kpi->deskripsi }}</div>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-gray-600">{{ $kpi->sumber_data }}</td>
                            <td class="py-3 pr-4 text-gray-600">{{ $kpi->satuan }}</td>
                            <td class="py-3 pr-4">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ strtolower($kpi->pola) === 'minimize' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst(strtolower($kpi->pola)) }}</span>
                            </td>
                            <td class="py-3 pr-4 text-right text-gray-900">
                                @if ($kpi->bobot_nilai !== null)
                                    {{ $angka($kpi->bobot_nilai) }}%{!! $kpi->bobot_beragam ? ' <span class="text-xs text-amber-600" title="Bobot tiap orang berbeda">*</span>' : '' !!}
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-xs text-gray-600">
                                @if ($kpi->rumus_realisasi)<div><span class="text-gray-400">Realisasi:</span> {{ $kpi->rumus_realisasi }}</div>@endif
                                @if ($kpi->rumus_target)<div><span class="text-gray-400">Target:</span> {{ $kpi->rumus_target }}</div>@endif
                            </td>
                            <td class="py-3 pr-4 text-right text-gray-900 whitespace-nowrap">
                                @if ($kpi->target_teks !== null)
                                    {{ $kpi->target_teks }}{!! $kpi->target_beragam ? ' <span class="text-xs text-amber-600" title="Target tiap orang berbeda">*</span>' : '' !!}
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button"
                                            @click="edit(@js([
                                                'id' => $kpi->id,
                                                'key_result_area' => $kpi->key_result_area,
                                                'nama_kpi' => $kpi->nama_kpi,
                                                'deskripsi' => $kpi->deskripsi,
                                                'sumber_data' => $kpi->sumber_data,
                                                'satuan' => $kpi->satuan,
                                                'pola' => strtolower($kpi->pola),
                                                'rumus_realisasi' => $kpi->rumus_realisasi,
                                                'rumus_target' => $kpi->rumus_target,
                                                'bobot' => $kpi->bobot_input,
                                                'target' => $kpi->target_input,
                                                'target_fleksibel' => (bool) $kpi->target_fleksibel,
                                                'is_active' => (bool) $kpi->is_active,
                                                'dipakai' => $kpi->target_bulanan_count,
                                                'jumlah_orang' => $kpi->jumlah_orang,
                                            ]))"
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium">Edit</button>
                                    <form method="POST" action="{{ route('admin.kpi-master.destroy', $kpi->id) }}"
                                          onsubmit="return confirm('Hapus KPI {{ addslashes($kpi->nama_kpi) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-medium">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @php $kraSebelumnya = $kpi->key_result_area; @endphp
                    @empty
                        <tr>
                            <td colspan="10" class="py-10 text-center text-gray-400">Divisi {{ $divisiNama }} belum punya KPI. Klik "+ Tambah KPI".</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($kpiList->isNotEmpty())
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="6" class="py-3 pl-4 pr-4 text-right font-semibold text-gray-700">Total Bobot</td>
                            <td class="py-3 pr-4 text-right font-bold {{ abs($totalBobot - 100) < 0.01 ? 'text-green-600' : 'text-red-600' }}">{{ $angka($totalBobot) }}%</td>
                            <td colspan="3" class="py-3 pr-4 text-xs {{ abs($totalBobot - 100) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                                {{ abs($totalBobot - 100) < 0.01 ? 'Total bobot sudah 100%' : 'Total bobot seharusnya 100%' }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            Bobot dan target menampilkan periode {{ $periode }}. Tanda * artinya nilainya berbeda antar orang.
            KPI yang sudah dipakai tidak bisa dihapus, hanya dinonaktifkan lewat Edit.
        </p>

        {{-- MODAL TAMBAH / EDIT --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 overflow-y-auto">
            <div @click.outside="open = false" class="bg-white rounded-2xl w-full max-w-2xl p-6 my-8">
                <h2 class="text-xl font-bold text-gray-900 mb-1" x-text="mode === 'edit' ? 'Edit KPI' : 'Tambah KPI'"></h2>
                <p class="text-sm text-gray-500 mb-4">Divisi {{ $divisiNama }}</p>

                <form method="POST" :action="action" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="mode !== 'edit'">
                    <input type="hidden" name="divisi_id" value="{{ $divisiId }}">
                    <input type="hidden" name="periode" value="{{ $periode }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Key Result Area</label>
                            <input type="text" name="key_result_area" x-model="form.key_result_area" list="kra-list"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                            <datalist id="kra-list">
                                @foreach ($kraList as $kra)
                                    <option value="{{ $kra }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Nama KPI (Indikator)</label>
                            <input type="text" name="nama_kpi" x-model="form.nama_kpi" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Deskripsi Indikator</label>
                        <textarea name="deskripsi" x-model="form.deskripsi" rows="2"
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Sumber Data</label>
                            <input type="text" name="sumber_data" x-model="form.sumber_data"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Satuan</label>
                            <select name="satuan" x-model="form.satuan" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                                @foreach ($satuanList as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Pola</label>
                            <select name="pola" x-model="form.pola" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                                @foreach ($polaList as $p)
                                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <p x-show="mode === 'edit' && form.dipakai > 0" class="text-xs text-amber-600">
                        KPI ini sudah dipakai di <span x-text="form.dipakai"></span> target, jadi satuannya tidak bisa diubah.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Rumus Realisasi</label>
                            <input type="text" name="rumus_realisasi" x-model="form.rumus_realisasi"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Rumus Target</label>
                            <input type="text" name="rumus_target" x-model="form.rumus_target"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="bg-orange-50 rounded-xl p-4">
                        <p class="text-sm font-medium text-gray-800 mb-1">Bobot &amp; target periode {{ $periode }}</p>
                        <p class="text-xs text-gray-600 mb-3">
                            Diterapkan ke semua orang yang memegang KPI ini
                            <span x-show="mode === 'edit' && form.jumlah_orang > 0">(<span x-text="form.jumlah_orang"></span> orang)</span>,
                            dan otomatis dipecah 4 minggu. Kosongkan keduanya kalau tidak ingin mengubah target.
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Bobot (%)</label>
                                <input type="number" name="bobot" x-model="form.bobot" step="0.01" min="0" max="100"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Target (<span x-text="satuanTarget()"></span>)</label>
                                <input type="text" inputmode="decimal" name="target" x-model="form.target"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white">
                                <p class="text-xs text-gray-500 mt-1" x-show="form.satuan === '%'">Ketik angka persen, misal 20 untuk 20%. Desimal pakai koma (12,5).</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="target_fleksibel" value="0">
                            <input type="checkbox" name="target_fleksibel" value="1"
                                   :checked="form.target_fleksibel" @change="form.target_fleksibel = $event.target.checked">
                            Target fleksibel (mengikuti realisasi, capaian 100% selama ada realisasi)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   :checked="form.is_active" @change="form.is_active = $event.target.checked">
                            Aktif
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="open = false"
                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Batal</button>
                        <button type="submit"
                                class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection