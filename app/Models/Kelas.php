<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas'; // Nama tabel yang sesuai dengan database

    protected $fillable = [
        'id_tahun_ajaran_matkul',
        'nama_kelas', // Nama kelas (misal: A, B, Reguler)
    ];

    /**
     * Relasi Many-to-One: Kelas dimiliki oleh satu TahunAjaranMatkul.
     */
    public function tahunAjaranMatkul()
    {
        return $this->belongsTo(TahunAjaranMatkul::class, 'id_tahun_ajaran_matkul');
    }

    /**
     * Relasi Many-to-Many: Satu Kelas memiliki banyak Mahasiswa (melalui tabel pivot mhs_kelas).
     */
    // public function mahasiswas()
    // {
    //     // Parameter: Model terkait, nama tabel pivot, FK Kelas di pivot, FK Mhs di pivot
    //     return $this->belongsToMany(Mhs::class, 'mhs_kelas', 'id_kelas', 'id_mhs')
    //                 ->withTimestamps(); // Jika tabel pivot mhs_kelas punya created_at/updated_at
    // }

}
