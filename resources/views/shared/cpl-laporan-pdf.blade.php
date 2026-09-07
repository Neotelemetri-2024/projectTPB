<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan CPL</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 15px; margin: 0 0 4px; }
        .meta { color: #555; margin-bottom: 12px; }
        .summary td { border: 1px solid #ddd; padding: 6px; }
        table.detail { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.detail th, table.detail td { border: 1px solid #ddd; padding: 4px 5px; vertical-align: top; }
        table.detail th { background: #f3f4f6; font-size: 9px; text-transform: uppercase; }
        .ok { color: #166534; font-weight: bold; }
        .bad { color: #991b1b; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan CPL (CPL → CPMK → Mata Kuliah)</h1>
    <div class="meta">
        Tahun ajaran: {{ $tahunAjaranLabel }} |
        Kurikulum: {{ $kurikulumLabel }} |
        Dicetak: {{ date('d/m/Y H:i') }}
    </div>

    <table class="summary" width="100%">
        <tr>
            <td>CPL: <strong>{{ $summary['total_cpl'] }}</strong></td>
            <td>CPMK: <strong>{{ $summary['total_cpmk'] }}</strong></td>
            <td>MK: <strong>{{ $summary['total_mk'] }}</strong></td>
            <td>Rata-rata capaian: <strong>{{ $summary['avg_capaian'] }}%</strong></td>
        </tr>
    </table>

    <table class="detail">
        <thead>
            <tr>
                <th>CPL</th>
                <th>CPMK</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Sumber Penilaian</th>
                <th>Nilai Min</th>
                <th>Target</th>
                <th>Capaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailRows as $row)
            <tr>
                <td><strong>{{ $row['kode_cpl'] }}</strong><br>{{ $row['cpl_deskripsi'] }}</td>
                <td><strong>{{ $row['kode_cpmk'] }}</strong><br>{{ $row['cpmk_deskripsi'] }}</td>
                <td>{{ $row['nama_mk'] }}<br>{{ $row['kode_mk'] }} · {{ $row['tahun_ajaran_label'] }}</td>
                <td>{{ $row['dosen'] }}</td>
                <td>{{ $row['sumber_penilaian'] }}</td>
                <td style="text-align:center">{{ $row['nilai_minimal'] }}</td>
                <td style="text-align:center">{{ $row['target_persen'] }}%</td>
                <td style="text-align:center" class="{{ ($row['capaian'] ?? 0) >= ($row['target_persen'] ?? 60) ? 'ok' : 'bad' }}">
                    {{ $row['capaian'] === null ? '—' : $row['capaian'] . '%' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
