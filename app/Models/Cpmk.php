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
        'deskripsi'
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

    // Relasi many-to-many untuk parent CPMK
    public function parents()
    {
        return $this->belongsToMany(Cpmk::class, 'cpmk_parents', 'child_cpmk_id', 'parent_cpmk_id');
    }

    // Relasi many-to-many untuk child CPMK (sub-CPMK)
    public function children()
    {
        return $this->belongsToMany(Cpmk::class, 'cpmk_parents', 'parent_cpmk_id', 'child_cpmk_id');
    }

    // Helper method untuk mengecek apakah CPMK memiliki parent
    public function hasParents()
    {
        return $this->parents()->count() > 0;
    }

    // Helper method untuk mengecek apakah CPMK memiliki children
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    // Helper method untuk mendapatkan root CPMK (CPMK tanpa parent)
    public function scopeRoot($query)
    {
        return $query->whereDoesntHave('parents');
    }

    // Helper method untuk mendapatkan leaf CPMK (CPMK tanpa children)
    public function scopeLeaf($query)
    {
        return $query->whereDoesntHave('children');
    }

    // Helper method untuk sync parents
    public function syncParents($parentIds)
    {
        return $this->parents()->sync($parentIds);
    }

    // Helper method untuk attach parents
    public function attachParents($parentIds)
    {
        return $this->parents()->attach($parentIds);
    }

    // Helper method untuk detach parents
    public function detachParents($parentIds = null)
    {
        if ($parentIds === null) {
            return $this->parents()->detach();
        }
        return $this->parents()->detach($parentIds);
    }

    // Helper method untuk sync children
    public function syncChildren($childIds)
    {
        return $this->children()->sync($childIds);
    }

    // Helper method untuk attach children
    public function attachChildren($childIds)
    {
        return $this->children()->attach($childIds);
    }

    // Helper method untuk detach children
    public function detachChildren($childIds = null)
    {
        if ($childIds === null) {
            return $this->children()->detach();
        }
        return $this->children()->detach($childIds);
    }

    // Helper method untuk mendapatkan semua parent IDs
    public function getParentIds()
    {
        return $this->parents()->pluck('cpmk.id');
    }

    // Helper method untuk mendapatkan semua child IDs
    public function getChildIds()
    {
        return $this->children()->pluck('cpmk.id');
    }
}
