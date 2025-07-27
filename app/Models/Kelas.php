<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'namaKelas',
        'tahunAjaranMatkulId'
    ];

    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'tahunAjaranMatkulId');
    }

    public function kelasMahasiswa()
    {
        return $this->hasMany(KelasMahasiswa::class, 'kelasId');
    }

    public function dosen()
    {
        return $this->belongsToMany(Dosen::class, 'dosen_pengampu_kelas', 'kelasId', 'dosenId');
    }

    public function dosenPengampuKelas()
    {
        return $this->hasMany(DosenPengampuKelas::class, 'kelasId');
    }
} 