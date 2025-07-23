<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpmkMatKul extends Model
{
    use HasFactory;

    protected $table = 'cpmk_mat_kul';

    protected $fillable = [
        'tahunAjaranMatkulId',
        'cpmkId',
    ];

    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'tahunAjaranMatkulId');
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmkId');
    }

    public function mataKuliah()
    {
        // Relasi ke MataKuliah melalui TahunAjaranMatkul
        return $this->hasOneThrough(
            MataKuliah::class,
            TahunAjaranMatkul::class,
            'id', // Foreign key on TahunAjaranMatkul
            'id', // Foreign key on MataKuliah
            'tahunAjaranMatkulId', // Local key on CpmkMatKul
            'mataKuliahId' // Local key on TahunAjaranMatkul
        );
    }
}
