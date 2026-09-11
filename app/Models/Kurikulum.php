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

    /**
     * Mata kuliah kurikulum ini yang punya minimal satu pasangan asesmen CPL.
     */
    public function mataKuliahAsesmen()
    {
        return $this->mataKuliah()->whereHas('cplAsesmen');
    }
}
