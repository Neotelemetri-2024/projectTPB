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
}
