<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenPerCpmk extends Model
{
    use HasFactory;
    protected $table = 'komponen_per_cpmk';
    protected $fillable = [
        'matkul_cpl_cpmk_id',
        'komponen_id',
        'bobot'
    ];

    public function matkulCplCpmk()
    {
        return $this->belongsTo(MatkulCplCpmk::class);
    }

    public function komponen()
    {
        return $this->belongsTo(Komponen::class);
    }
}
