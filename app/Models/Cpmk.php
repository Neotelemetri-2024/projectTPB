<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    use HasFactory;

    protected $table = 'cpmk';

    protected $fillable = [
        'kodeCpmk',
        'deskripsi',
        'parent_id'
    ];

    public function cpl()
    {
        return $this->belongsToMany(Cpl::class, 'cpmk_cpl', 'cpmkId', 'cplId')->withTimestamps();
    }

    public function cpmkMatKul()
    {
        return $this->hasMany(CpmkMatKul::class, 'cpmkId');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'cpmkId');
    }

    public function bobot()
    {
        return $this->hasMany(Bobot::class, 'cpmkId');
    }

    // Relasi rekursif untuk parent CPMK
    public function parent()
    {
        return $this->belongsTo(Cpmk::class, 'parent_id');
    }

    // Relasi rekursif untuk child CPMK (sub-CPMK)
    public function children()
    {
        return $this->hasMany(Cpmk::class, 'parent_id');
    }

    // Relasi untuk mendapatkan semua descendant (child, grandchild, dll)
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Relasi untuk mendapatkan semua ancestor (parent, grandparent, dll)
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    // Helper method untuk mengecek apakah CPMK memiliki parent
    public function hasParent()
    {
        return !is_null($this->parent_id);
    }

    // Helper method untuk mengecek apakah CPMK memiliki children
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    // Helper method untuk mendapatkan root CPMK (CPMK tanpa parent)
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    // Helper method untuk mendapatkan leaf CPMK (CPMK tanpa children)
    public function scopeLeaf($query)
    {
        return $query->whereDoesntHave('children');
    }
}
