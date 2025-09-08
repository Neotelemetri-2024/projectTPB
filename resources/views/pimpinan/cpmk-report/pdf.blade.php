<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan CPMK - {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}-{{ $tahunAjaranMatkul->mataKuliah->kurikulum }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .info-item h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .info-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }
        .cpmk-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .cpmk-header {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
        }
        .cpmk-header h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .stat-item .value {
            font-size: 16px;
            font-weight: bold;
        }
        .stat-item .label {
            font-size: 10px;
            color: #666;
        }
        .charts-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .chart-section {
            display: table-cell;
            width: 50%;
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .chart-section h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: bold;
        }
        .distribution-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            padding: 3px 0;
        }
        .distribution-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .histogram-item {
            display: flex;
            align-items: center;
            margin-bottom: 3px;
        }
        .histogram-range {
            width: 40px;
            font-size: 10px;
        }
        .histogram-bar {
            flex: 1;
            height: 12px;
            background-color: #e5e7eb;
            margin: 0 8px;
            position: relative;
        }
        .histogram-fill {
            height: 100%;
            background-color: #3b82f6;
        }
        .histogram-count {
            width: 30px;
            font-size: 10px;
            text-align: right;
        }
        .bobot-section {
            margin-top: 15px;
        }
        .bobot-section h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: bold;
        }
        .bobot-grid {
            display: table;
            width: 100%;
        }
        .bobot-item {
            display: table-cell;
            width: 50%;
            padding: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK)</h1>
        <p>{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}-{{ $tahunAjaranMatkul->mataKuliah->kurikulum }} - {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</p>
        <p>Tahun Ajaran: {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ ucfirst($tahunAjaranMatkul->tahunAjaran->periode) }}</p>
        <p>Kurikulum: {{ $tahunAjaranMatkul->mataKuliah->kurikulum }} | SKS: {{ $tahunAjaranMatkul->getSks() }}</p>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-item">
                <h3>Total Mahasiswa</h3>
                <div class="value">{{ number_format($tahunAjaranMatkul->kelas->sum(function($kelas) { return $kelas->kelasMahasiswa->count(); })) }}</div>
            </div>
            <div class="info-item">
                <h3>Total Kelas</h3>
                <div class="value">{{ number_format($tahunAjaranMatkul->kelas->count()) }}</div>
            </div>
            <div class="info-item">
                <h3>Total CPMK</h3>
                <div class="value">{{ number_format($tahunAjaranMatkul->cpmkMatKul->count()) }}</div>
            </div>
        </div>
    </div>

    @if(count($cpmkData) > 0)
        @foreach($cpmkData as $index => $data)
            @if($index > 0)
                <div class="page-break"></div>
            @endif
            
            <div class="cpmk-section">
                <div class="cpmk-header">
                    <h3>{{ $data['cpmk']->kodeCpmk }} - {{ $data['cpmk']->deskripsi }}</h3>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="value">{{ $data['mahasiswa_dengan_nilai'] }}</div>
                        <div class="label">Mahasiswa dengan Nilai</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">{{ number_format($data['average_nilai'], 2) }}</div>
                        <div class="label">Rata-rata Nilai</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">{{ $data['competent_count'] }}</div>
                        <div class="label">Kompeten (≥60)</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">{{ $data['not_competent_count'] }}</div>
                        <div class="label">Tidak Kompeten (<60)</div>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-section">
                        <h4>Distribusi Nilai</h4>
                        @foreach($data['distribution'] as $grade => $info)
                            <div class="distribution-item">
                                <div style="display: flex; align-items: center;">
                                    <div class="distribution-color" style="background-color: {{ $info['color'] }};"></div>
                                    <span>{{ $info['label'] }}</span>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: bold;">{{ $info['count'] }} mahasiswa</div>
                                    <div style="font-size: 10px; color: #666;">{{ $info['percentage'] }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="chart-section">
                        <h4>Histogram Nilai</h4>
                        @foreach($data['histogram_data'] as $histogram)
                            <div class="histogram-item">
                                <div class="histogram-range">{{ $histogram['range'] }}</div>
                                <div class="histogram-bar">
                                    <div class="histogram-fill" style="width: {{ $histogram['percentage'] }}%;"></div>
                                </div>
                                <div class="histogram-count">{{ $histogram['count'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($data['bobot_komponen']->count() > 0)
                    <div class="bobot-section">
                        <h4>Bobot Komponen Penilaian</h4>
                        <div class="bobot-grid">
                            @foreach($data['bobot_komponen'] as $bobot)
                                <div class="bobot-item">
                                    <div style="font-weight: bold;">{{ $bobot['komponen'] }}</div>
                                    <div>{{ $bobot['bobot'] }}%</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="no-data">
            <h3>Tidak ada data CPMK</h3>
            <p>Belum ada data CPMK yang tersedia untuk mata kuliah ini.</p>
        </div>
    @endif
</body>
</html>
