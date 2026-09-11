<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpl extends Model
{
    use HasFactory;

    protected $table = 'cpl';

    protected $fillable = [
        'kodeCpl',
        'deskripsi',
        'nilaiMinimal',
        'targetPersen',
    ];

    protected $casts = [
        'nilaiMinimal' => 'integer',
        'targetPersen' => 'integer',
    ];

    public function cpmk()
    {
        return $this->belongsToMany(Cpmk::class, 'cpmk_cpl', 'cplId', 'cpmkId')->withTimestamps();
    }

    /**
     * Mata kuliah yang diases untuk CPL ini (pasangan CPL x matkul).
     */
    public function mataKuliahAsesmen()
    {
        return $this->belongsToMany(MataKuliah::class, 'cpl_mata_kuliah_asesmen', 'cplId', 'mataKuliahId')->withTimestamps();
    }
}
