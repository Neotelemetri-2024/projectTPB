<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan CPMK - {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
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
        .info-section h2 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            background-color: #f3f4f6;
            padding: 5px;
        }
        .cpmk-item {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .cpmk-header {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
            color: #1f2937;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 11px;
        }
        .stat-item {
            background-color: #f9fafb;
            padding: 5px 8px;
            border-radius: 3px;
            border: 1px solid #e5e7eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENGUKURAN CPMK</h1>
        <p>{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</p>
        <p>{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }} • {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ ucfirst($tahunAjaranMatkul->tahunAjaran->periode) }}</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if(count($cpmkData) > 0)
        @foreach($cpmkData as $index => $data)
            <div class="cpmk-item">
                <div class="cpmk-header">
                    {{ $data['cpmk']->kodeCpmk }} - {{ $data['cpmk']->deskripsi }}
                </div>
                
                <div class="stats">
                    <div class="stat-item">Total: {{ $data['totalMahasiswa'] }} Mahasiswa</div>
                    <div class="stat-item">Dengan Nilai: {{ $data['mahasiswaDenganNilai'] }} Mahasiswa</div>
                    <div class="stat-item">Rata-rata: {{ $data['averageNilai'] }}</div>
                    <div class="stat-item">Kompeten: {{ $data['competentPercentage'] }}%</div>
                </div>

                <div class="info-section">
                    <h2>Distribusi Nilai</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Nilai Angka</th>
                                <th>Nilai Mutu</th>
                                <th>Sebutan Mutu</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nilai < 60</td>
                                <td>U</td>
                                <td>Uncompetence</td>
                                <td>{{ $data['distribution']['U']['percentage'] }}%</td>
                            </tr>
                            <tr>
                                <td>60 ≤ Nilai < 75</td>
                                <td>C</td>
                                <td>Competence</td>
                                <td>{{ $data['distribution']['C']['percentage'] }}%</td>
                            </tr>
                            <tr>
                                <td>75 ≤ Nilai < 90</td>
                                <td>E</td>
                                <td>Excellent</td>
                                <td>{{ $data['distribution']['E']['percentage'] }}%</td>
                            </tr>
                            <tr>
                                <td>Nilai ≥ 90</td>
                                <td>X</td>
                                <td>Extraordinary</td>
                                <td>{{ $data['distribution']['X']['percentage'] }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="info-section">
                    <h2>Histogram Nilai</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Range Nilai</th>
                                <th>Jumlah Mahasiswa</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['histogramData'] as $histogram)
                                <tr>
                                    <td>{{ $histogram['range'] }}</td>
                                    <td>{{ $histogram['count'] }}</td>
                                    <td>{{ $histogram['percentage'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($index < count($cpmkData) - 1)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div style="text-align: center; padding: 40px;">
            <p>Belum ada data CPMK untuk mata kuliah ini.</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Portal TPB</p>
        <p>© {{ date('Y') }} Portal TPB - Universitas Andalas</p>
    </div>
</body>
</html>
