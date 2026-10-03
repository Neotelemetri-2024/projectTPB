<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstitutionSetting;
use Illuminate\Http\Request;

class InstitutionSettingController extends Controller
{
    public function edit()
    {
        return view('admin.institution-settings.edit', ['institution' => InstitutionSetting::details()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ketua_nama' => ['required', 'string', 'max:255'],
            'ketua_nip' => ['required', 'digits_between:15,20'],
        ]);

        InstitutionSetting::query()->updateOrCreate(['id' => 1], $data);

        return redirect()->route('admin.institution-settings.edit')->with('success', 'Ketua departemen berhasil diperbarui.');
    }
}
