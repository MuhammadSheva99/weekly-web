<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Surat Peringatan Karyawan</h1>
    <p class="text-sm text-gray-500 mt-1">Management SP Karyawan</p>

    <!-- Tab Navigation -->
    <div class="flex border-b border-gray-200 mt-6 space-x-8">
        <a href="{{ route('management.sp-karyawan.terbitkan') }}" 
           class="pb-3 text-sm font-medium transition-colors relative {{ request()->routeIs('management.sp-karyawan.terbitkan') || request()->routeIs('management.sp-karyawan.index') ? 'font-semibold border-b-2' : 'text-gray-400 hover:text-gray-600' }}"
           style="{{ request()->routeIs('management.sp-karyawan.terbitkan') || request()->routeIs('management.sp-karyawan.index') ? 'color: #07B151; border-color: #07B151;' : '' }}">
            Terbitkan SP
        </a>
        <a href="{{ route('management.sp-karyawan.riwayat') }}" 
           class="pb-3 text-sm font-medium transition-colors relative {{ request()->routeIs('management.sp-karyawan.riwayat') ? 'font-semibold border-b-2' : 'text-gray-400 hover:text-gray-600' }}"
           style="{{ request()->routeIs('management.sp-karyawan.riwayat') ? 'color: #07B151; border-color: #07B151;' : '' }}">
            Riwayat Pelanggaran
        </a>
        <a href="{{ route('management.sp-karyawan.rekomendasi') }}" 
           class="pb-3 text-sm font-medium transition-colors relative {{ request()->routeIs('management.sp-karyawan.rekomendasi') ? 'font-semibold border-b-2' : 'text-gray-400 hover:text-gray-600' }}"
           style="{{ request()->routeIs('management.sp-karyawan.rekomendasi') ? 'color: #07B151; border-color: #07B151;' : '' }}">
            Rekomendasi dari Atasan
        </a>
    </div>
</div>