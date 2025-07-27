<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'cpmkId',
        'mahasiswaId',
        'dosenPengampuKelasId',
        'tahunAjaranMatkulId',
        'bobotId',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmkId');
    }

    public function bobot()
    {
        return $this->belongsTo(Bobot::class, 'bobotId');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswaId');
    }

    public function dosenPengampuKelas()
    {
        return $this->belongsTo(DosenPengampuKelas::class, 'dosenPengampuKelasId');
    }

    public function dosen()
    {
        return $this->hasOneThrough(Dosen::class, DosenPengampuKelas::class, 'id', 'id', 'dosenPengampuKelasId', 'dosenId');
    }

    public function kelas()
    {
        return $this->hasOneThrough(Kelas::class, DosenPengampuKelas::class, 'id', 'id', 'dosenPengampuKelasId', 'kelasId');
    }

    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'tahunAjaranMatkulId');
    }

    // Helper method untuk mendapatkan komponen melalui bobot
    public function getKomponenAttribute()
    {
        return $this->bobot->komponen ?? null;
    }
}
