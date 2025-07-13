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
        'tahun' => 'integer'
    ];

    public function tahunAjaranMatkul()
    {
        return $this->hasMany(TahunAjaranMatkul::class, 'tahunAjaranId');
    }
}
