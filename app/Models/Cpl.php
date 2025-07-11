<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpl extends Model
{
    use HasFactory;
    protected $table = 'cpl';
    protected $fillable = [
        'kode_cpl',
        'deskripsi',
    ];
    public function matkuls()
    {
        return $this->belongsToMany(Matkul::class, 'matkul_cpl_cpmk')->withTimestamps();
    }

    public function cpmks()
    {
        return $this->belongsToMany(Cpmk::class, 'matkul_cpl_cpmk')->withTimestamps();
    }
}
