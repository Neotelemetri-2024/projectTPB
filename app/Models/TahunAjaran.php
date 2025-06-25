<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TahunAjaranMatkul;

class TahunAjaran extends Model
{
    use HasFactory;
    protected $table = 'tahun_ajarans';
    protected $fillable = [
        'tahun',
        'semester',
    ];

    // Di model TahunAjaran.php
    // Relasi many-to-many dengan Matkul melalui pivot tahun_ajaran_matkul
    public function matkul()
    {
        return $this->belongsToMany(Matkul::class, 'tahun_ajaran_matkul')
            ->withPivot('sks', 'semester_studi')
            ->withTimestamps();
    }

    // Relasi one-to-many ke tabel pivot
    public function tahunAjaranMatkuls()
    {
        return $this->hasMany(TahunAjaranMatkul::class);
    }

    // public function tahunAjaranMatkuls()
    // {
    //     return $this->hasMany(TahunAjaranMatkul::class, 'tahun_ajaran_id');
    // }
}
