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
        'kurikulumId',
        'namaMatkul',
        'jenis',
        'sks',
    ];

    protected $casts = [
        'sks' => 'integer',
    ];

    public function tahunAjaranMatkul()
    {
        return $this->hasMany(TahunAjaranMatkul::class, 'mataKuliahId');
    }

    public function kurikulumRef()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulumId');
    }

    /**
     * CPL yang mengases mata kuliah ini (pasangan CPL x matkul).
     */
    public function cplAsesmen()
    {
        return $this->belongsToMany(Cpl::class, 'cpl_mata_kuliah_asesmen', 'mataKuliahId', 'cplId')->withTimestamps();
    }

    /**
     * Accessor kompatibilitas: view lama memakai $mk->kurikulum (kode kurikulum).
     */
    public function getKurikulumAttribute(): ?string
    {
        if ($this->relationLoaded('kurikulumRef')) {
            return $this->kurikulumRef?->kode;
        }

        return $this->kurikulumRef()->value('kode');
    }
}
