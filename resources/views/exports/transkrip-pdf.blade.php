<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .header .nomor {
            font-size: 12px;
            margin: 10px 0;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            font-size: 10px;
        }
        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .info-left {
            width: 48%;
            vertical-align: top;
        }
        .info-right {
            width: 48%;
            vertical-align: top;
            padding-left: 20px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
        }
        .data-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            font-size: 9px;
        }
        .data-table td.left {
            text-align: left;
        }
        .data-table td.center {
            text-align: center;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .summary-section {
            margin-top: 20px;
            font-size: 10px;
        }
        .summary-table {
            width: 100%;
        }
        .summary-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .signature-section {
            margin-top: 30px;
            text-align: left;
            font-size: 10px;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
            margin-top: 60px;
        }
        .border-line {
            border-bottom: 1px solid #000;
            width: 100%;
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="border-line"></div>
        <h1>TRANSKRIP NILAI</h1>
        <div class="nomor">Nomor : ....................................................</div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-left">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 35%;"><strong>Nama Mahasiswa</strong></td>
                            <td style="width: 5%;">:</td>
                            <td>{{ strtoupper($mahasiswa->nama ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td><strong>No. Induk Mahasiswa</strong></td>
                            <td>:</td>
                            <td>{{ $mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Program Studi</strong></td>
                            <td>:</td>
                            <td>SISTEM INFORMASI</td>
                        </tr>
                    </table>
                </td>
                <td class="info-right">
                    <!-- Kolom kanan kosong atau bisa diisi informasi lain jika diperlukan -->
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            @if(count($matkulDiambil) >= 29)
                <!-- Header untuk 2 kolom -->
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 25%;">Mata Kuliah</th>
                    <th style="width: 8%;">sks</th>
                    <th style="width: 8%;">Nilai Huruf</th>
                    <th style="width: 8%;">Nilai Mutu</th>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 25%;">Mata Kuliah</th>
                    <th style="width: 8%;">sks</th>
                    <th style="width: 8%;">Nilai Huruf</th>
                    <th style="width: 8%;">Nilai Mutu</th>
                </tr>
            @else
                <!-- Header untuk 1 kolom -->
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th style="width: 50%;">Mata Kuliah</th>
                    <th style="width: 12%;">sks</th>
                    <th style="width: 15%;">Nilai Huruf</th>
                    <th style="width: 15%;">Nilai Mutu</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @if(count($matkulDiambil) >= 29)
                <!-- Layout 2 kolom -->
                @php
                    $chunks = array_chunk($matkulDiambil, 2);
                    $currentNo = 1;
                @endphp
                @forelse($chunks as $chunk)
                <tr>
                    @if(isset($chunk[0]))
                        <td class="center">{{ $currentNo++ }}</td>
                        <td class="left">{{ $chunk[0]['nama'] }}</td>
                        <td class="center">{{ $chunk[0]['sks'] }}</td>
                        <td class="center">{{ $chunk[0]['grade'] ?? '-' }}</td>
                        <td class="center">{{ $chunk[0]['nilai_akhir'] ?? '' }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif

                    @if(isset($chunk[1]))
                        <td class="center">{{ $currentNo++ }}</td>
                        <td class="left">{{ $chunk[1]['nama'] }}</td>
                        <td class="center">{{ $chunk[1]['sks'] }}</td>
                        <td class="center">{{ $chunk[1]['grade'] ?? '-' }}</td>
                        <td class="center">{{ $chunk[1]['nilai_akhir'] ?? '' }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="no-data">Belum ada mata kuliah diambil</td>
                </tr>
                @endforelse

                <!-- Row untuk total (2 kolom) -->
                <tr style="background-color: #f8f8f8;">
                    <td colspan="2" class="center"><strong>Jumlah</strong></td>
                    <td class="center"><strong>{{ $totalSks }}</strong></td>
                    <td colspan="2" class="center"><strong>{{ number_format(collect($matkulDiambil)->sum('nilai_akhir'), 2) }}</strong></td>
                    <td colspan="5" class="center"></td>
                </tr>

                <!-- Row untuk indeks prestasi (2 kolom) -->
                <tr style="background-color: #f0f0f0;">
                    <td colspan="5" class="center"><strong>Indeks Prestasi</strong></td>
                    <td colspan="5" class="center"><strong>{{ number_format($ipk, 2) }}</strong></td>
                </tr>

            @else
                <!-- Layout 1 kolom -->
                @php $currentNo = 1; @endphp
                @forelse($matkulDiambil as $mk)
                <tr>
                    <td class="center">{{ $currentNo++ }}</td>
                    <td class="left">{{ $mk['nama'] }}</td>
                    <td class="center">{{ $mk['sks'] }}</td>
                    <td class="center">{{ $mk['grade'] ?? '-' }}</td>
                    <td class="center">{{ $mk['nilai_akhir'] ?? '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-data">Belum ada mata kuliah diambil</td>
                </tr>
                @endforelse

                <!-- Row untuk total (1 kolom) -->
                <tr style="background-color: #f8f8f8;">
                    <td colspan="2" class="center"><strong>Jumlah</strong></td>
                    <td class="center"><strong>{{ $totalSks }}</strong></td>
                    <td class="center"></td>
                    <td class="center"><strong>{{ number_format(collect($matkulDiambil)->sum('nilai_akhir'), 2) }}</strong></td>
                </tr>

                <!-- Row untuk indeks prestasi (1 kolom) -->
                <tr style="background-color: #f0f0f0;">
                    <td colspan="2" class="center"><strong>Indeks Prestasi</strong></td>
                    <td colspan="3" class="center"><strong>{{ number_format($ipk, 2) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature-section">
        <div style="text-align: right; margin-top: 40px;">
            <p>Padang, {{ date('d') }} {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F') }} {{ date('Y') }}</p>
            <div class="signature-box">
                <div style="margin-top: 80px; border-top: 1px solid #000; padding-top: 5px;">
                    <strong>NIP. ................................</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
