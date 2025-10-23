<div class="space-y-4">
    <h4 class="text-lg font-semibold text-gray-900">Detail Nilai Per CPMK</h4>

    <!-- Info Mahasiswa dan Grade -->
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-700"><strong>NIM:</strong> {{ $mahasiswa->nim }}</p>
                <p class="text-sm text-gray-700"><strong>Nama:</strong> {{ $mahasiswa->nama }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-700"><strong>Nilai Akhir:</strong></p>
                <p class="text-lg font-bold text-blue-600">{{ number_format($totalNilaiAkhir, 2) }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-700"><strong>Grade:</strong></p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                    {{ $grade == 'A' || $grade == 'A-' ? 'bg-green-100 text-green-800' :
                       ($grade == 'B+' || $grade == 'B' || $grade == 'B-' ? 'bg-blue-100 text-blue-800' :
                       ($grade == 'C+' || $grade == 'C' ? 'bg-yellow-100 text-yellow-800' :
                       ($grade == 'D' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))) }}">
                    {{ $grade ?: '-' }}
                </span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPMK</th>
                    @foreach($allKomponen as $komponen)
                    @if(($totalBobotKomponen[$komponen->id] ?? 0) > 0)
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $komponen->nama }}
                    </th>
                    @endif
                    @endforeach
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total CPMK</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($nilaiPerCpmk as $index => $cpmkNilai)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $cpmkNilai['cpmk']->nama }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($cpmkNilai['cpmk']->deskripsi, 50) }}</div>
                        </div>
                    </td>
                    @foreach($allKomponen as $komponen)
                    @if(($totalBobotKomponen[$komponen->id] ?? 0) > 0)
                    @php
                    $detailKomponen = collect($cpmkNilai['detail_komponen'])->firstWhere('komponen.id', $komponen->id);
                    $nilaiKomponen = $detailKomponen['nilai'] ?? null;
                    $bobotKomponen = $detailKomponen['bobot'] ?? 0;
                    $kontribusi = ($nilaiKomponen !== null && $bobotKomponen > 0) ? ($nilaiKomponen * $bobotKomponen / 100) : 0;
                    @endphp
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($nilaiKomponen !== null)
                        <div class="text-xs text-gray-500">{{ number_format($bobotKomponen, 2) }}%</div>
                        <div class="text-sm font-medium text-blue-600">{{ number_format($kontribusi, 2) }}</div>
                        @else
                        <div class="text-xs text-gray-400">{{ number_format($bobotKomponen, 2) }}%</div>
                        <div class="text-sm text-gray-400">-</div>
                        @endif
                    </td>
                    @endif
                    @endforeach
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($cpmkNilai['nilai'] !== null)
                        <div class="text-xs text-gray-500">{{ number_format($cpmkNilai['bobot_total'], 2) }}%</div>
                        <div class="text-lg font-bold text-green-600">{{ number_format($cpmkNilai['nilai'], 2) }}</div>
                        @else
                        <div class="text-xs text-gray-400">{{ number_format($cpmkNilai['bobot_total'], 2) }}%</div>
                        <div class="text-sm text-gray-400">Belum ada nilai</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Total</td>
                    @foreach($allKomponen as $komponen)
                    @if(($totalBobotKomponen[$komponen->id] ?? 0) > 0)
                    @php
                    $totalKontribusiKomponen = 0;
                    $cpmkCount = 0;

                    foreach($nilaiPerCpmk as $cpmkNilai) {
                    $detailKomponen = collect($cpmkNilai['detail_komponen'])->firstWhere('komponen.id', $komponen->id);
                    if($detailKomponen && $detailKomponen['nilai'] !== null) {
                    $totalKontribusiKomponen += ($detailKomponen['nilai'] * $detailKomponen['bobot'] / 100);
                    $cpmkCount++;
                    }
                    }
                    @endphp
                    <td class="px-6 py-3 whitespace-nowrap text-center">
                        <div class="text-xs text-gray-500">{{ number_format($totalBobotKomponen[$komponen->id] ?? 0, 2) }}%</div>
                        <div class="text-sm font-semibold text-blue-600">{{ number_format($totalKontribusiKomponen, 2) }}</div>
                    </td>
                    @endif
                    @endforeach
                    <td class="px-6 py-3 whitespace-nowrap text-center">
                        <div class="text-xs text-gray-500">100%</div>
                        <div class="text-lg font-bold text-blue-600">{{ number_format($totalNilaiAkhir, 2) }}</div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>