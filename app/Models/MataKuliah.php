<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kodeMatkul',
        'namaMatkul',
        'jenis',
        'sks'
    ];

    protected $casts = [
        'sks' => 'integer'
    ];

    public function tahunAjaranMatkul()
    {
        return $this->hasMany(TahunAjaranMatkul::class, 'mataKuliahId');
    }

    public function cpmkMatKul()
    {
        return $this->hasMany(CpmkMatKul::class, 'mataKuliahId');
    }
}
