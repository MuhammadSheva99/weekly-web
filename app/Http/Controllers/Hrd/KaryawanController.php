<?php

namespace App\Http\Controllers\Hrd;

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

        return view('hrd.karyawan.index', [
            'karyawan' => $karyawan,
            'divisiList' => $divisiList,
        ]);
    }

    public function create()
    {
        $divisiList = Divisi::orderBy('nama')->get();

        return view('hrd.karyawan.create', [
            'divisiList' => $divisiList,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'divisi_id' => 'nullable|exists:divisi,id',
            'email' => 'nullable|email|max:255',
        ]);

        Karyawan::create($data);

        return redirect()->route('hrd.karyawan.index')->with('status', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        $divisiList = Divisi::orderBy('nama')->get();

        return view('hrd.karyawan.edit', [
            'karyawan' => $karyawan,
            'divisiList' => $divisiList,
        ]);
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'divisi_id' => 'nullable|exists:divisi,id',
            'email' => 'nullable|email|max:255',
        ]);

        $karyawan->update($data);

        return redirect()->route('hrd.karyawan.index')->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('hrd.karyawan.index')->with('status', 'Karyawan berhasil dihapus.');
    }
}