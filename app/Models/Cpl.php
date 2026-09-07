<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpl extends Model
{
    use HasFactory;

    protected $table = 'cpl';

    protected $fillable = [
        'kodeCpl',
        'deskripsi',
        'nilaiMinimal',
        'targetPersen',
    ];

    protected $casts = [
        'nilaiMinimal' => 'integer',
        'targetPersen' => 'integer',
    ];

    public function cpmk()
    {
        return $this->belongsToMany(Cpmk::class, 'cpmk_cpl', 'cplId', 'cpmkId')->withTimestamps();
    }
}
