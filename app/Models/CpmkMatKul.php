<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpmkMatKul extends Model
{
    use HasFactory;

    protected $table = 'cpmk_mat_kul';

    protected $fillable = [
        'mataKuliahId',
        'cpmkId'
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mataKuliahId');
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmkId');
    }
}
