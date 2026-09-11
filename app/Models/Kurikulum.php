<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = 'kurikulum';

    protected $fillable = [
        'kode',
        'nama',
        'tahun',
        'deskripsi',
        'isAktif',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'isAktif' => 'boolean',
    ];

    public function mataKuliah()
    {
        return $this->hasMany(MataKuliah::class, 'kurikulumId');
    }

    public function mataKuliahAsesmen()
    {
        return $this->hasMany(MataKuliah::class, 'kurikulumId')->where('isAsesmen', true);
    }
}
