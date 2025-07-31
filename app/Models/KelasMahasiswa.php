<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'kelas_mahasiswa';

    protected $fillable = [
        'mahasiswaId',
        'kelasId',
        'totalNilai',
        'grade'
    ];

    protected $casts = [
        'grade' => 'string'
    ];

    // Define the available grades
    const GRADES = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswaId');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelasId');
    }

    public function tahunAjaranMatkul()
    {
        return $this->hasOneThrough(
            TahunAjaranMatkul::class,
            Kelas::class,
            'id', // Foreign key on kelas table
            'id', // Foreign key on tahun_ajaran_matkul table
            'kelasId', // Local key on kelas_mahasiswa table
            'tahunAjaranMatkulId' // Local key on kelas table
        );
    }

    /**
     * Get calculated total nilai based on bobot and nilai records
     */
    public function getTotalNilaiAttribute($value)
    {
        // If value is already set, return it
        if ($value !== null) {
            return $value;
        }

        // Calculate total nilai based on bobot and nilai records
        $kelas = $this->kelas;
        if (!$kelas) {
            return null;
        }

        $tahunAjaranMatkul = $kelas->tahunAjaranMatkul;
        if (!$tahunAjaranMatkul) {
            return null;
        }

        // Get all bobot for this tahun ajaran matkul
        $bobotIds = \App\Models\Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)->pluck('id');
        
        // Get nilai records for this mahasiswa
        $nilaiRecords = \App\Models\Nilai::where('mahasiswaId', $this->mahasiswaId)
            ->whereIn('bobotId', $bobotIds)
            ->with('bobot')
            ->get();

        $totalNilai = 0;
        $totalBobot = 0;

        foreach ($nilaiRecords as $nilai) {
            if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                $totalNilai += ($nilai->nilai * $nilai->bobot->bobot);
                $totalBobot += $nilai->bobot->bobot;
            }
        }

        return $totalBobot > 0 ? round($totalNilai / $totalBobot, 2) : null;
    }

    /**
     * Get calculated grade based on total nilai
     */
    public function getGradeAttribute($value)
    {
        // If value is already set, return it
        if ($value !== null) {
            return $value;
        }

        $totalNilai = $this->total_nilai;
        if ($totalNilai === null) {
            return null;
        }

        // Convert to grade
        if ($totalNilai >= 80) return 'A';
        elseif ($totalNilai >= 75) return 'A-';
        elseif ($totalNilai >= 70) return 'B+';
        elseif ($totalNilai >= 65) return 'B';
        elseif ($totalNilai >= 60) return 'B-';
        elseif ($totalNilai >= 55) return 'C+';
        elseif ($totalNilai >= 50) return 'C';
        elseif ($totalNilai >= 40) return 'D';
        else return 'E';
    }
}
