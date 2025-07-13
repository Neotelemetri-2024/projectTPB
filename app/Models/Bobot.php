<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bobot extends Model
{
    use HasFactory;

    protected $table = 'bobot';

    protected $fillable = [
        'bobot',
        'tahunAjaranMatkulId',
        'cpmkId',
        'komponenId',
    ];

    protected $casts = [
        'bobot' => 'float',
    ];

    // Relationships
    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'tahunAjaranMatkulId');
    }

    public function komponen()
    {
        return $this->belongsTo(Komponen::class, 'komponenId');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'bobotId');
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmkId');
    }
}
