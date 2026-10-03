<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionSetting extends Model
{
    protected $fillable = ['ketua_nama', 'ketua_nip'];

    public static function details(): array
    {
        $details = config('institution');
        $setting = static::query()->first();

        if ($setting) {
            $details['ketua_nama'] = $setting->ketua_nama;
            $details['ketua_nip'] = $setting->ketua_nip;
        }

        return $details;
    }
}
