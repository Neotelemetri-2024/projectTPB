<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function matkuls()
    {
        return $this->hasMany(Matkul::class, 'lecturer_id');
    }
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}
