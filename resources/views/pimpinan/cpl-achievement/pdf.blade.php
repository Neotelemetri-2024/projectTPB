<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ketercapaian CPL</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #555; margin-bottom: 16px; font-size: 10px; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .summary td { border: 1px solid #ddd; padding: 8px; width: 25%; }
        .summary .label { font-size: 9px; color: #666; text-transform: uppercase; }
        .summary .value { font-size: 14px; font-weight: bold; margin-top: 2px; }
        .cpl-block { margin-bottom: 16px; page-break-inside: avoid; }
        .cpl-head { background: #f3f4f6; border: 1px solid #ddd; padding: 8px; }
        .cpl-head strong { font-size: 12px; }
        .cpl-desc { font-size: 9px; color: #555; margin-top: 2px; }
        table.detail { width: 100%; border-collapse: collapse; margin-top: 0; }
        table.detail th, table.detail td { border: 1px solid #ddd; padding: 5px 6px; }
        table.detail th { background: #f9fafb; font-size: 9px; text-transform: uppercase; text-align: left; }
        table.detail td.center { text-align: center; }
        table.detail tr.total { background: #f3f4f6; font-weight: bold; }
        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
        .ok { background: #dcfce7; color: #166534; }
        .bad { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 20px; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <h1>Laporan Ketercapaian CPL per Mata Kuliah</h1>
    <div class="meta">
        Tahun ajaran: {{ $tahunAjaranLabel }} &nbsp;|&nbsp;
        Kurikulum: {{ $kurikulumLabel }} &nbsp;|&nbsp;
        Dicetak: {{ date('d/m/Y H:i') }} &nbsp;|&nbsp;
        Target ketercapaian: sesuai master CPL (nilai min & target %)
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total CPL</div>
                <div class="value">{{ $summary['total_cpl'] }}</div>
                <div class="meta" style="margin:0">{{ $summary['cpl_tercapai'] }} mencapai target</div>
            </td>
            <td>
                <div class="label">Rata-rata Capaian</div>
                <div class="value">{{ $summary['avg_persen'] }}%</div>
            </td>
            <td>
                <div class="label">MK Pendukung</div>
                <div class="value">{{ $summary['total_mk'] }}</div>
            </td>
            <td>
                <div class="label">Mahasiswa Diases</div>
                <div class="value">{{ number_format($summary['total_diases']) }}</div>
                <div class="meta" style="margin:0">{{ number_format($summary['total_mencapai']) }} mencapai</div>
            </td>
        </tr>
    </table>

    @forelse($grouped as $group)
        <div class="cpl-block">
            <div class="cpl-head">
                <strong>{{ $group['kode_cpl'] }}</strong>
                — Capaian:
                <span class="badge {{ $group['sum_persen'] >= ($group['target_persen'] ?? 60) ? 'ok' : 'bad' }}">{{ $group['sum_persen'] }}%</span>
                &nbsp;|&nbsp; Diases: {{ $group['sum_total'] }}
                &nbsp;|&nbsp; Mencapai: {{ $group['sum_mencapai'] }}
                &nbsp;|&nbsp; {{ $group['cpmk_count'] }} CPMK · {{ $group['mk_count'] }} MK
                &nbsp;|&nbsp; Min {{ $group['nilai_minimal'] ?? 60 }} · Target {{ $group['target_persen'] ?? 60 }}%
                @if($group['deskripsi'])
                    <div class="cpl-desc">{{ $group['deskripsi'] }}</div>
                @endif
                @if($group['cpmk_kodes'])
                    <div class="cpl-desc">CPMK: {{ $group['cpmk_kodes'] }}</div>
                @endif
            </div>
            <table class="detail">
                <thead>
                    <tr>
                        <th>Kode MK</th>
                        <th>Nama MK</th>
                        <th>Kurikulum</th>
                        <th>Tahun Ajaran</th>
                        <th style="text-align:center">Diases</th>
                        <th style="text-align:center">Mencapai</th>
                        <th style="text-align:center">% Capaian</th>
                        <th style="text-align:center">Target</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['items'] as $row)
                    <tr>
                        <td>{{ $row['kode_mk'] }}</td>
                        <td>{{ $row['nama_mk'] }}</td>
                        <td>{{ $row['kurikulum'] ?: '-' }}</td>
                        <td>{{ $row['tahun_ajaran_label'] }}</td>
                        <td class="center">{{ $row['total'] }}</td>
                        <td class="center">{{ $row['mencapai'] }}</td>
                        <td class="center">{{ $row['capai_persen'] }}%</td>
                        <td class="center">{{ $group['target_persen'] ?? 60 }}%</td>
                    </tr>
                    @endforeach
                    <tr class="total">
                        <td colspan="4" style="text-align:right">Ringkasan {{ $group['kode_cpl'] }}</td>
                        <td class="center">{{ $group['sum_total'] }}</td>
                        <td class="center">{{ $group['sum_mencapai'] }}</td>
                        <td class="center">{{ $group['sum_persen'] }}%</td>
                        <td class="center">{{ $group['target_persen'] ?? 60 }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <p>Tidak ada data untuk filter yang dipilih.</p>
    @endforelse

    <div class="footer">
        Laporan ini menampilkan data ketercapaian yang sama dengan halaman sistem (filter hanya membatasi tampilan).
    </div>
</body>
</html>
