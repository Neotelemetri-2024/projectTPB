<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CplCpmk extends Model
{
    use HasFactory;
    // Tentukan nama tabel jika tidak mengikuti konvensi penamaan
    protected $table = 'cpl_cpmk';

    // Tentukan relasi antara CPL dan CPMK
    public function cpl()
    {
        return $this->belongsTo(Cpl::class);
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class);
    }
    
}
