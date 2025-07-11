<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komponen extends Model
{
    use HasFactory;
    protected $table = 'komponen';

    protected $fillable = [
        'nama',
    ];

    public function komponenPerCpmk()
    {
        return $this->hasMany(KomponenPerCpmk::class);
    }
}
