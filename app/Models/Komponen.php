<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komponen extends Model
{
    use HasFactory;

    protected $table = 'komponen';

    protected $fillable = [
        'nama'
    ];

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'komponenId');
    }

    public function bobot()
    {
        return $this->hasMany(Bobot::class, 'komponenId');
    }
}
