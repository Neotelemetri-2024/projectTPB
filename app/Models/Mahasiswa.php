<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'userId',
        'nama',
        'nim',
        'tahunMasuk'
    ];

    protected $casts = [
        'tahunMasuk' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function kelasMahasiswa()
    {
        return $this->hasMany(KelasMahasiswa::class, 'mahasiswaId');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'mahasiswaId');
    }

}
