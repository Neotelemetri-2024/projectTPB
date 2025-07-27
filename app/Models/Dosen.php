<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'userId',
        'nama',
        'nip'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function dosenPengampu()
    {
        return $this->hasMany(DosenPengampu::class, 'dosenId');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'dosen_pengampu_kelas', 'dosenId', 'kelasId');
    }

    public function dosenPengampuKelas()
    {
        return $this->hasMany(DosenPengampuKelas::class, 'dosenId');
    }
}
