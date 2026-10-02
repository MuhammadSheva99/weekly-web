<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with('divisi')->orderBy('nama');

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->get('divisi_id'));
        }

        $karyawan = $query->get();
        $divisiList = Divisi::orderBy('nama')->get();

        return view('management.karyawan.index', [
            'karyawan' => $karyawan,
            'divisiList' => $divisiList,
        ]);
    }
}