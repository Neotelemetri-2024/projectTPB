<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Capaian Pembelajaran</title>
    <style>
        @page {
            margin: 18mm 14mm 14mm 14mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        .accent { color: #0B3D91; }
        .bold { font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
        .muted { color: #444; }

        .kop {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .kop td { vertical-align: middle; }
        .kop-logo { width: 72px; }
        .kop-logo img { width: 64px; height: auto; }
        .kop-text { text-align: center; padding: 0 8px; }
        .kop-text .line1 {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.2px;
            margin: 0;
        }
        .kop-text .line2 {
            font-size: 13px;
            font-weight: bold;
            margin: 1px 0;
        }
        .kop-text .line3 {
            font-size: 12px;
            font-weight: bold;
            margin: 1px 0;
        }
        .kop-text .line4 {
            font-size: 15px;
            font-weight: bold;
            color: #0B3D91;
            margin: 2px 0 3px;
            text-transform: uppercase;
        }
        .kop-text .meta {
            font-size: 8px;
            color: #333;
            margin: 0;
        }

        .kop-line {
            border-top: 2.5px solid #111;
            border-bottom: 0.8px solid #111;
            height: 4px;
            margin: 6px 0 14px;
        }

        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 16px;
            letter-spacing: 0.4px;
        }

        .content-wrap {
            position: relative;
            width: 100%;
        }

        .watermark {
            position: absolute;
            top: 90px;
            left: 50%;
            margin-left: -110px;
            width: 220px;
            opacity: 0.07;
            z-index: 0;
        }

        .two-col {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 1;
        }
        .two-col > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .col-left {
            width: 42%;
            padding-right: 12px !important;
            border-right: 1px solid #bbb;
        }
        .col-right {
            width: 58%;
            padding-left: 12px !important;
        }

        .intro { margin: 0 0 8px; font-size: 10px; }
        .student-name {
            font-size: 18px;
            font-weight: bold;
            color: #0B3D91;
            text-transform: uppercase;
            margin: 0 0 4px;
            line-height: 1.15;
        }
        .nim {
            font-size: 11px;
            margin: 0 0 10px;
        }

        .inst-label { margin: 0 0 4px; font-size: 10px; }
        .inst-name {
            font-size: 11px;
            font-weight: bold;
            color: #0B3D91;
            text-transform: uppercase;
            margin: 0 0 2px;
            line-height: 1.25;
        }

        .capai-label { margin: 12px 0 4px; font-size: 10px; }
        .metric {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .metric td {
            padding: 1px 0;
            font-size: 10px;
            vertical-align: top;
        }
        .metric .k { width: 42%; }
        .metric .s { width: 4%; }
        .metric .v { font-weight: bold; }
        .metric .v.accent { color: #0B3D91; }

        .gelar-row { margin-top: 6px; font-size: 10px; }
        .gelar-row .accent {
            font-weight: bold;
            color: #0B3D91;
            font-size: 12px;
        }

        .right-intro {
            font-size: 9.5px;
            text-align: justify;
            margin: 0 0 8px;
            line-height: 1.4;
        }

        .cpl-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        .cpl-table th,
        .cpl-table td {
            border: 1px solid #333;
            padding: 3px 4px;
            vertical-align: top;
        }
        .cpl-table th {
            background: #f3f4f6;
            font-weight: bold;
            text-align: center;
            font-size: 7.5px;
        }
        .cpl-table td.no { text-align: center; width: 6%; }
        .cpl-table td.kode { text-align: center; width: 13%; font-weight: bold; font-size: 8px; }
        .cpl-table td.desc { text-align: justify; width: 66%; line-height: 1.25; }
        .cpl-table td.nilai {
            text-align: center;
            width: 15%;
            font-size: 12px;
            font-weight: bold;
            color: #0B3D91;
            vertical-align: middle;
        }

        .footer-block {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        .footer-block td { vertical-align: top; }
        .akreditasi {
            width: 48%;
            font-size: 8px;
            color: #333;
            padding-top: 24px;
        }
        .akreditasi .badge {
            display: inline-block;
            border: 1.5px solid #0B3D91;
            color: #0B3D91;
            font-weight: bold;
            font-size: 10px;
            padding: 4px 8px;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .ttd {
            width: 52%;
            text-align: right;
            font-size: 10px;
            padding: 0;
        }
        .ttd-inner {
            width: 270px;
            text-align: left;
            font-size: 10px;
        }
        .ttd .jabatan {
            font-size: 9.5px;
            margin: 2px 0 42px;
            line-height: 1.35;
            text-align: left;
        }
        .ttd .nama {
            font-size: 11px;
            font-weight: bold;
            color: #0B3D91;
            text-decoration: underline;
            margin: 0;
            text-align: left;
        }
        .ttd .nip {
            font-size: 9px;
            margin: 2px 0 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td class="kop-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <p class="line1">{{ $institution['kementerian'] }}</p>
                <p class="line2">{{ $institution['universitas'] }}</p>
                <p class="line3">{{ $institution['fakultas'] }}</p>
                <p class="line4">{{ $institution['departemen'] }}</p>
                <p class="meta">{{ $institution['alamat'] }}</p>
                <p class="meta">Website: {{ $institution['website'] }} dan email: {{ $institution['email'] }}</p>
            </td>
            <td class="kop-logo"></td>
        </tr>
    </table>
    <div class="kop-line"></div>

    <h1 class="doc-title">Surat Keterangan Capaian Pembelajaran</h1>

    <div class="content-wrap">
        @if($logoBase64)
            <img class="watermark" src="{{ $logoBase64 }}" alt="">
        @endif

        <table class="two-col">
            <tr>
                <td class="col-left">
                    <p class="intro">Menyatakan, bahwa</p>
                    <p class="student-name">{{ strtoupper($mahasiswa->nama ?? '-') }}</p>
                    <p class="nim">NIM: {{ $mahasiswa->nim ?? '-' }}</p>

                    <p class="inst-label">telah menyelesaikan studi di:</p>
                    <p class="inst-name">{{ $institution['prodi'] }}</p>
                    <p class="inst-name">{{ $institution['fakultas'] }}</p>
                    <p class="inst-name">{{ $institution['universitas'] }}</p>

                    <p class="capai-label">dengan capaian:</p>
                    <table class="metric">
                        <tr>
                            <td class="k">IPK</td>
                            <td class="s">:</td>
                            <td class="v">{{ $akademik['ipk'] !== null ? number_format($akademik['ipk'], 2) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="k">Total SKS Lulus</td>
                            <td class="s">:</td>
                            <td class="v">{{ $akademik['total_sks'] }} SKS</td>
                        </tr>
                        <tr>
                            <td class="k">Predikat</td>
                            <td class="s">:</td>
                            <td class="v accent">{{ $akademik['predikat'] }}</td>
                        </tr>
                    </table>

                    <p class="gelar-row">
                        Gelar Lulusan:
                        <span class="accent">{{ $akademik['gelar'] }}</span>
                    </p>
                </td>
                <td class="col-right">
                    <p class="right-intro">
                        Berdasarkan hasil evaluasi pembelajaran, mahasiswa yang bersangkutan telah mencapai
                        Capaian Pembelajaran Lulusan (CPL) dengan rincian sebagai berikut:
                    </p>

                    <table class="cpl-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode CPL</th>
                                <th>Deskripsi Capaian Pembelajaran</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cplData as $i => $cpl)
                                <tr>
                                    <td class="no">{{ $i + 1 }}</td>
                                    <td class="kode">{{ $cpl['kode'] }}</td>
                                    <td class="desc">{{ $cpl['deskripsi'] }}</td>
                                    <td class="nilai">{{ $cpl['nilai_surat'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="center">Belum ada data CPL</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <table class="footer-block">
        <tr>
            <td class="akreditasi">
                <div class="badge">IABEE / BAN-PT</div>
                <div>{{ $institution['akreditasi'] }}</div>
            </td>
            <td class="ttd" align="right">
                <table class="ttd-inner" align="right" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="text-align: left; width: 270px;">
                            <div>{{ $institution['kota'] }}, {{ $tanggalCetak }}</div>
                            <div class="jabatan">
                                Ketua Departemen Teknik Pertanian dan Biosistem,<br>
                                Fakultas Teknologi Pertanian,<br>
                                Universitas Andalas
                            </div>
                            <p class="nama">{{ $institution['ketua_nama'] }}</p>
                            <p class="nip">NIP. {{ $institution['ketua_nip'] }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
