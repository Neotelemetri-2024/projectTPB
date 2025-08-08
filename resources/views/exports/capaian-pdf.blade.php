<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Capaian CPL & CPMK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
        .header .subtitle {
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
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: left;
            vertical-align: top;
        }
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
            padding: 6px 4px;
        }
        .data-table td {
            padding: 4px 3px;
        }
        .text-center {
            text-align: center;
        }
        .status-tercapai {
            background-color: #d4edda !important;
            color: #155724 !important;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .status-belum {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .nilai-tercapai {
            background-color: #d4edda !important;
            color: #155724 !important;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .nilai-belum {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
        .signature-section {
            margin-top: 30px;
            text-align: right;
            width: 200px;
            float: right;
        }
        .signature-box {
            border: 1px solid #000;
            height: 60px;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN CAPAIAN PEMBELAJARAN LULUSAN (CPL) & CAPAIAN PEMBELAJARAN MATA KULIAH (CPMK)</h1>
        <div class="subtitle">
            @if($cplIdTerpilih)
                @php
                    $selectedCpl = $cplList->where('id', $cplIdTerpilih)->first();
                @endphp
                Filter CPL: {{ $selectedCpl ? $selectedCpl->kodeCpl : 'Tidak ditemukan' }}
            @else
                Semua CPL
            @endif
        </div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-left">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 30%;">Nama</td>
                            <td style="width: 5%;">:</td>
                            <td style="width: 65%;">{{ $mahasiswa->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>NIM</td>
                            <td>:</td>
                            <td>{{ $mahasiswa->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Program Studi</td>
                            <td>:</td>
                            <td>{{ $mahasiswa->prodi ?? 'Teknik Informatika' }}</td>
                        </tr>
                    </table>
                </td>
                <td class="info-right">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 30%;">Tanggal Cetak</td>
                            <td style="width: 5%;">:</td>
                            <td style="width: 65%;">{{ date('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Jam Cetak</td>
                            <td>:</td>
                            <td>{{ date('H:i:s') }} WIB</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    @if(count($cplData) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">CPL</th>
                    <th style="width: 10%;">Kode MK</th>
                    <th style="width: 18%;">Nama Mata Kuliah</th>
                    <th style="width: 8%;">Kode CPMK</th>
                    <th style="width: 25%;">Deskripsi CPMK</th>
                    <th style="width: 8%;">Nilai</th>
                    <th style="width: 8%;">Total</th>
                    <th style="width: 15%;">Status Capaian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cplData as $cpl)
                    @php
                        $cplRowspan = collect($cpl['cpmk'])->groupBy(function($item) {
                            return $item['kode_mk'].'|'.$item['nama_mk'];
                        })->flatten(1)->count();
                        $printedCpl = false;
                        $printedCplTotal = false;
                        $grouped = collect($cpl['cpmk'])->groupBy(function($item) {
                            return $item['kode_mk'].'|'.$item['nama_mk'];
                        });
                    @endphp
                    @foreach($grouped as $mkKey => $cpmkList)
                        @php
                            [$kode_mk, $nama_mk] = explode('|', $mkKey);
                            $rowspan = count($cpmkList);
                            $printedMk = false;
                        @endphp
                        @foreach($cpmkList as $idx => $cpmk)
                        <tr>
                            @if(!$printedCpl)
                                <td rowspan="{{ $cplRowspan }}" style="text-align: center; vertical-align: middle;">
                                    <strong>{{ $cpl['kode'] }}</strong><br>
                                    <small style="font-size: 7px;">{{ $cpl['deskripsi'] }}</small>
                                </td>
                                @php $printedCpl = true; @endphp
                            @endif
                            @if(!$printedMk)
                                <td rowspan="{{ $rowspan }}" style="text-align: center; vertical-align: middle;">{{ $kode_mk }}</td>
                                <td rowspan="{{ $rowspan }}" style="vertical-align: middle;">{{ $nama_mk }}</td>
                                @php $printedMk = true; @endphp
                            @endif
                            <td style="text-align: center;">{{ $cpmk['kode'] }}</td>
                            <td style="font-size: 8px;">{{ $cpmk['deskripsi'] }}</td>
                            <td style="text-align: center;">
                                <span class="{{ is_numeric($cpmk['nilai']) && $cpmk['nilai'] >= 55 ? 'nilai-tercapai' : 'nilai-belum' }}">
                                    {{ $cpmk['nilai'] }}
                                </span>
                            </td>
                            @if(!$printedCplTotal)
                                <td rowspan="{{ $cplRowspan }}" style="text-align: center; vertical-align: middle;">
                                    <span class="{{ is_numeric($cpl['total_cpl']) && $cpl['total_cpl'] >= 55 ? 'nilai-tercapai' : 'nilai-belum' }}">
                                        {{ $cpl['total_cpl'] }}
                                    </span>
                                </td>
                                <td rowspan="{{ $cplRowspan }}" style="text-align: center; vertical-align: middle;">
                                    <span class="{{ $cpl['status_cpl'] === 'Tercapai' ? 'status-tercapai' : 'status-belum' }}">
                                        {{ $cpl['status_cpl'] }}
                                    </span>
                                </td>
                                @php $printedCplTotal = true; @endphp
                            @endif
                        </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; margin: 40px 0; color: #666;">
            <p>Tidak ada data CPL yang dipilih.</p>
        </div>
    @endif

    <!-- Keterangan -->
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
        <h4 style="margin: 0 0 10px 0; font-size: 11px;">Keterangan:</h4>
        <table style="width: 100%; font-size: 9px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <p style="margin: 2px 0;"><strong>Status Capaian:</strong></p>
                    <p style="margin: 2px 0;">• <span class="status-tercapai">Tercapai</span> = Nilai ≥ 55</p>
                    <p style="margin: 2px 0;">• <span class="status-belum">Belum Tercapai</span> = Nilai < 55</p>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <p style="margin: 2px 0;"><strong>Total CPL:</strong></p>
                    <p style="margin: 2px 0;">Dihitung dari nilai CPMK tertinggi yang mendukung CPL tersebut</p>
                    <p style="margin: 2px 0;"><strong>Nilai CPMK:</strong></p>
                    <p style="margin: 2px 0;">Dihitung dari rata-rata tertimbang komponen penilaian</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ date('d F Y, H:i:s') }} WIB</p>
    </div>

    <div class="signature-section">
        <p>Mengetahui,</p>
        <p>Koordinator Program Studi</p>
        <div class="signature-box"></div>
        <p>_________________________</p>
        <p>NIDN: ________________</p>
    </div>
</body>
</html>
