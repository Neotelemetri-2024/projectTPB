@extends('layouts.main')
@section('title', 'Dashboard Dosen')
@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold">Dashboard Dosen</h2>
        <form method="GET" action="" class="mt-2 md:mt-0">
            <div class="flex items-center gap-2">
                <label for="tahun_ajaran_id" class="text-sm font-medium text-gray-700">Tahun Ajaran:</label>
                <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 p-2.5" onchange="this.form.submit()">
                    @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                            {{ $ta->tahun }} - {{ ucfirst($ta->periode) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="text-3xl font-bold text-blue-600">{{ $jumlahMK }}</div>
            <div class="text-gray-600 mt-2">Mata Kuliah</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="text-3xl font-bold text-amber-600">{{ $jumlahKelas }}</div>
            <div class="text-gray-600 mt-2">Kelas</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="text-3xl font-bold text-green-600">{{ $jumlahMahasiswa }}</div>
            <div class="text-gray-600 mt-2">Mahasiswa</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center">
            <div class="text-3xl font-bold text-purple-600">{{ $progressPersen }}%</div>
            <div class="text-gray-600 mt-2">Progress Input Nilai</div>
            <div class="text-xs text-gray-400">{{ $jumlahKelasLengkap }} dari {{ $kelasList->count() }} kelas lengkap</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold mb-2">Jumlah Mahasiswa per Mata Kuliah</h3>
            <canvas id="barChart"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold mb-2">Distribusi Grade</h3>
            <canvas id="pieChart"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="font-semibold mb-2">Progress Rata-rata Nilai per MK (Tiap Tahun Ajaran)</h3>
        <canvas id="lineChart"></canvas>
    </div>

    <!-- Distribusi Nilai per Mata Kuliah -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="font-semibold mb-4">Distribusi Nilai per Mata Kuliah</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($gradeDistributionPerMK as $mk)
                @if($mk['totalMahasiswa'] > 0)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium text-sm">{{ $mk['kodeMatkul'] }}</h4>
                            <span class="text-xs text-gray-500">{{ $mk['totalMahasiswa'] }} mahasiswa</span>
                        </div>
                        <p class="text-xs text-gray-600 mb-3">{{ $mk['mataKuliah'] }}</p>
                        
                        <div class="space-y-2">
                            @php
                                $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
                                $colors = ['#10B981', '#34D399', '#60A5FA', '#3B82F6', '#6366F1', '#F59E0B', '#F97316', '#EF4444', '#DC2626'];
                            @endphp
                            @foreach($grades as $index => $grade)
                                @if($mk['gradeCounts'][$grade] > 0)
                                    @php
                                        $percentage = $mk['totalMahasiswa'] > 0 ? round(($mk['gradeCounts'][$grade] / $mk['totalMahasiswa']) * 100, 1) : 0;
                                        $color = $colors[$index];
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $color }};"></div>
                                            <span class="text-xs font-medium">{{ $grade }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background-color: {{ $color }};"></div>
                                            </div>
                                            <span class="text-xs text-gray-600 w-8 text-right">{{ $mk['gradeCounts'][$grade] }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold mb-4">Daftar Mata Kuliah Diampu (Semester Aktif)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mata Kuliah</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status Nilai</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($kelasList as $kelas)
                        <tr>
                            <td class="px-4 py-2">{{ $kelas->tahunAjaranMatkul->mataKuliah->namaMatkul ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $kelas->namaKelas }}</td>
                            <td class="px-4 py-2">{{ $kelas->kelasMahasiswa->count() }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $mahasiswaCount = $kelas->kelasMahasiswa->count();
                                    $sudahNilai = $kelas->kelasMahasiswa->whereNotNull('totalNilai')->count();
                                @endphp
                                @if($mahasiswaCount > 0 && $mahasiswaCount == $sudahNilai)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Lengkap</span>
                                @else
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Belum Lengkap</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Bar Chart
const barChart = new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: @json($barChartLabels),
        datasets: [{
            label: 'Jumlah Mahasiswa',
            data: @json($barChartData),
            backgroundColor: '#3B82F6',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
// Pie Chart
const pieChart = new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: @json($pieChartLabels),
        datasets: [{
            data: @json($pieChartData),
            backgroundColor: ['#10B981','#34D399','#60A5FA','#3B82F6','#6366F1','#F59E0B','#F97316','#EF4444','#DC2626'],
        }]
    },
    options: { responsive: true }
});
// Line Chart
const lineChart = new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: @json($lineChartLabels),
        datasets: @json($lineChartDatasets),
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true, max: 100 } }
    }
});
</script>
@endsection
