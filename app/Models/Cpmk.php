<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    use HasFactory;

    protected $table = 'cpmk';

    protected $fillable = [
        'idCpl',
        'kodeCpmk',
        'deskripsi'
    ];

    public function cpl()
    {
        return $this->belongsTo(Cpl::class, 'idCpl');
    }

    public function cpmkMatKul()
    {
        return $this->hasMany(CpmkMatKul::class, 'cpmkId');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'cpmkId');
    }
}
