<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;
    protected $table = 'matkuls';
    protected $fillable = [
        'kode_matkul',
        'nama_matkul',
        'jenis', // Wajib atau Pilihan
    ];

    // Relasi many-to-many dengan TahunAjaran melalui pivot tahun_ajaran_matkul
    public function tahunAjaran()
    {
        return $this->belongsToMany(TahunAjaran::class, 'tahun_ajaran_matkul')
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
    //     return $this->hasMany(TahunAjaranMatkul::class, 'matkul_id');
    // }

    public function cpmks()
    {
        return $this->hasMany(Cpmk::class, 'id_matkul');
    }
}
