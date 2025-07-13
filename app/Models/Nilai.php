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
        'komponenId',
        'mahasiswaId',
        'dosenPengampuId',
        'tahunAjaranMatkulId',
        'nilai',
        'bobot'
    ];

    protected $casts = [
        'nilai' => 'float',
        'bobot' => 'float'
    ];

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmkId');
    }

    public function komponen()
    {
        return $this->belongsTo(Komponen::class, 'komponenId');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswaId');
    }

    public function dosenPengampu()
    {
        return $this->belongsTo(DosenPengampu::class, 'dosenPengampuId');
    }

    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'tahunAjaranMatkulId');
    }
}
