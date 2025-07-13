<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaranMatkul extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran_matkul';

    protected $fillable = [
        'tahunAjaranId',
        'mataKuliahId',
        'kelas'
    ];

    protected $casts = [
        'kelas' => 'integer'
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahunAjaranId');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mataKuliahId');
    }

    public function dosenPengampu()
    {
        return $this->hasMany(DosenPengampu::class, 'tahunAjaranMatkulId');
    }

    public function kelasMahasiswa()
    {
        return $this->hasMany(KelasMahasiswa::class, 'tahunAjaranMatkulId');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'tahunAjaranMatkulId');
    }

    public function bobot()
    {
        return $this->hasMany(Bobot::class, 'tahunAjaranMatkulId');
    }

    public function cpmkMatKul()
    {
        return $this->hasMany(CpmkMatKul::class, 'tahunAjaranMatkulId');
    }

    /**
     * Get class name as letter (1=A, 2=B, etc.)
     */
    public function getKelasHurufAttribute()
    {
        return $this->kelas ? chr(64 + $this->kelas) : '';
    }

    /**
     * Convert class numbers to letters
     */
    public static function convertKelasToHuruf($kelasNumbers)
    {
        if (is_numeric($kelasNumbers)) {
            return chr(64 + $kelasNumbers);
        }

        if ($kelasNumbers instanceof \Illuminate\Support\Collection) {
            return $kelasNumbers->map(function($kelas) {
                return chr(64 + $kelas);
            });
        }

        return $kelasNumbers;
    }
}
