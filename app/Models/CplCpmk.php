<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CplCpmk extends Model
{
    use HasFactory;

    protected $table = 'cpmk_cpl';

    protected $fillable = [
        'cplId',
        'cpmkId',
    ];

    public function cpl()
    {
        return $this->belongsTo(Cpl::class, 'cplId');
    }

    public function cpmk()
    {
        return $this->belongsTo(Cpmk::class, 'cpmkId');
    }
}
