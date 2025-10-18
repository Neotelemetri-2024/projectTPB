<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TahunAjaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->paginate(10);
        return view('admin.tahun-ajaran.index', compact('tahunAjaran'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'periode' => 'required|string|max:50',
        ], [
            'tahun.required' => 'Tahun ajaran wajib diisi',
            'tahun.regex' => 'Format tahun ajaran harus berupa "2024/2025"',
            'periode.required' => 'Periode wajib diisi',
            'periode.max' => 'Periode maksimal 50 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if tahun ajaran already exists
            $existing = TahunAjaran::where('tahun', $request->tahun)
                ->where('periode', $request->periode)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Tahun ajaran dengan periode tersebut sudah ada')
                    ->withInput();
            }

            TahunAjaran::create([
                'tahun' => $request->tahun,
                'periode' => $request->periode,
            ]);

            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', 'Tahun ajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(TahunAjaran $tahunAjaran)
    {
        return response()->json([
            'status' => 'success',
            'data' => $tahunAjaran
        ]);
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'periode' => 'required|string|max:50',
        ], [
            'tahun.required' => 'Tahun ajaran wajib diisi',
            'tahun.regex' => 'Format tahun ajaran harus berupa "2024/2025"',
            'periode.required' => 'Periode wajib diisi',
            'periode.max' => 'Periode maksimal 50 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if tahun ajaran already exists (excluding current record)
            $existing = TahunAjaran::where('tahun', $request->tahun)
                ->where('periode', $request->periode)
                ->where('id', '!=', $tahunAjaran->id)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Tahun ajaran dengan periode tersebut sudah ada')
                    ->withInput();
            }

            $tahunAjaran->update([
                'tahun' => $request->tahun,
                'periode' => $request->periode,
            ]);

            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', 'Tahun ajaran berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        try {
            $tahunAjaran->delete();

            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', 'Tahun ajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
