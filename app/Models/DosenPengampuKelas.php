<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenPengampuKelas extends Model
{
    use HasFactory;

    protected $table = 'dosen_pengampu_kelas';

    protected $fillable = [
        'dosenId',
        'kelasId'
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosenId');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelasId');
    }
}
