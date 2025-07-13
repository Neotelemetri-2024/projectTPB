<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenPengampu extends Model
{
    use HasFactory;

    protected $table = 'dosen_pengampu';

    protected $fillable = [
        'dosenId',
        'tahunAjaranMatkulId'
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosenId');
    }

    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'tahunAjaranMatkulId');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'dosenPengampuId');
    }
}
