<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Matkul;
use App\Models\TahunAjaran;
use App\Models\Kelas;

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

    // Relasi Many-to-One: TahunAjaranMatkul dimiliki oleh satu Matkul
    // public function matkul()
    // {
    //     return $this->belongsTo(Matkul::class, 'matkul_id');
    // }

    // // Relasi Many-to-One: TahunAjaranMatkul dimiliki oleh satu TahunAjaran
    // public function tahunAjaran()
    // {
    // return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    // }

    // Relasi One-to-Many: Satu TahunAjaranMatkul bisa punya banyak Kelas
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'tahun_ajaran_matkul_id');
    }
}
