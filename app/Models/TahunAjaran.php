<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'tahun',
        'periode'
    ];

    protected $casts = [
        // Tahun sekarang berupa string dengan format "2024/2025"
    ];

    public function tahunAjaranMatkul()
    {
        return $this->hasMany(TahunAjaranMatkul::class, 'tahunAjaranId');
    }
}
