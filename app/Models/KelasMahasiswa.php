<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'kelas_mahasiswa';

    protected $fillable = [
        'mahasiswaId',
        'tahunAjaranMatkulId'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswaId');
    }

    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'tahunAjaranMatkulId');
    }
}
