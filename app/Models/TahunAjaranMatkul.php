<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Matkul;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Dosen;

class TahunAjaranMatkul extends Model
{
    use HasFactory;
    // Nama tabel yang sesuai dengan database
    protected $table = 'tahun_ajaran_matkuls';

    // Kolom yang boleh diisi secara massal (mass assignable)
    protected $fillable = [
        'matkul_id',
        'tahun_ajaran_id',
        'semester_studi', // Kolom tambahan di pivot
        'sks',      // Kolom tambahan di pivot
    ];

    // Relasi ke Matkul
    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }

    // Relasi ke TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Relasi One-to-Many: Satu TahunAjaranMatkul bisa punya banyak Kelas
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_tahun_ajaran_matkul');
    }

    // Relasi Many-to-Many: TahunAjaranMatkul diampu oleh banyak Dosen
    // public function dosenPengampus()
    // {
    //     return $this->belongsToMany(
    //         Dosen::class,
    //         'tahun_ajaran_matkul_dosen',
    //         'id_tahun_ajaran_matkul', // FK dari model ini di tabel pivot
    //         'id_dosen'                // FK dari model Dosen di tabel pivot
    //     )->using(TahunAjaranMatkulDosen::class); // Menggunakan custom pivot model (karena PFK)
    // }
}
