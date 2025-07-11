<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatkulCplCpmk extends Model
{
    use HasFactory;
    protected $table = 'matkul_cpl_cpmk';

    protected $fillable = [
        'matkul_id',
        'cpl_id',
        'cpmk_id'
    ];

    public function cpl()
    {
        return $this->belongsTo(Cpl::class);
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class);
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }
    public function komponenPenilaian()
    {
        return $this->hasMany(KomponenPerCpmk::class);
    }
}
