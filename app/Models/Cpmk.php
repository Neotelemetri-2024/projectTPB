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

    public function matkuls()
    {
        return $this->belongsToMany(Matkul::class, 'matkul_cpl_cpmk')->withTimestamps();
    }

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'matkul_cpl_cpmk')->withTimestamps();
    }
    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'id_matkul');
    }
}
