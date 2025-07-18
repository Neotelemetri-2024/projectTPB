<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    use HasFactory;

    protected $table = 'cpmk';

    protected $fillable = [
        'kodeCpmk',
        'deskripsi'
    ];

    public function cpl()
    {
        return $this->belongsToMany(Cpl::class, 'cpmk_cpl', 'cpmkId', 'cplId')->withTimestamps();
    }

    public function cpmkMatKul()
    {
        return $this->hasMany(CpmkMatKul::class, 'cpmkId');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'cpmkId');
    }

    public function bobot()
    {
        return $this->hasMany(Bobot::class, 'cpmkId');
    }
}
