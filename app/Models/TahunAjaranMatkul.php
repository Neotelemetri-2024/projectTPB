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
        'semester'
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahunAjaranId');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mataKuliahId');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'tahunAjaranMatkulId');
    }

    public function kelasMahasiswa()
    {
        return $this->hasManyThrough(KelasMahasiswa::class, Kelas::class, 'tahunAjaranMatkulId', 'kelasId');
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
     * Get all classes for this mata kuliah
     */
    public function getAllKelas()
    {
        return $this->kelas()->orderBy('namaKelas')->get();
    }

    /**
     * Get class names as letters
     */
    public function getKelasNames()
    {
        return $this->kelas()->pluck('namaKelas')->sort()->values();
    }
}
