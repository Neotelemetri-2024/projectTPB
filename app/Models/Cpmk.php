<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    use HasFactory;

    protected $table = 'cpmk';
    protected $fillable = [
        'id_matkul',
        'kode_cpmk',
        'nama_cpmk',
    ];

    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'id_matkul');
    }
}
