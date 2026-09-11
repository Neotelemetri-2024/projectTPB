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
        'kelasId',
        'totalNilai',
        'grade'
    ];

    protected $casts = [
        'grade' => 'string'
    ];

    // Define the available grades
    const GRADES = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswaId');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelasId');
    }

    public function tahunAjaranMatkul()
    {
        return $this->hasOneThrough(
            TahunAjaranMatkul::class,
            Kelas::class,
            'id', // Foreign key on kelas table
            'id', // Foreign key on tahun_ajaran_matkul table
            'kelasId', // Local key on kelas_mahasiswa table
            'tahunAjaranMatkulId' // Local key on kelas table
        );
    }

}
