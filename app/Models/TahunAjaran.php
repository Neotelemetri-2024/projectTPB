<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TahunAjaranMatkul;

class TahunAjaran extends Model
{
    use HasFactory;
    protected $table = 'tahun_ajarans';
    protected $fillable = [
        'tahun',
        'semester',
    ];

    public function tahunAjaranMatkuls()
    {
        return $this->hasMany(TahunAjaranMatkul::class, 'id_tahun_ajaran');
    }
}
