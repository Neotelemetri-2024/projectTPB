        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 w-4 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">No</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Tahun Ajaran</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Mata Kuliah</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Semester</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Kelas</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Dosen Pengampu</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Mahasiswa</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tahunAjaranMatkuls as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 whitespace-nowrap">
                            <input type="checkbox" value="{{ $item->id }}" class="item-checkbox w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">{{ $tahunAjaranMatkuls->firstItem() + $index }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tahunAjaran->tahun }}-{{ $item->tahunAjaran->periode }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $item->mataKuliah->namaMatkul }}</div>
                                <div class="text-sm text-gray-500">{{ $item->mataKuliah->kodeMatkul }}-{{ $item->mataKuliah->kurikulum }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">
                            Semester {{ $item->semester ?? 1 }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $item->kelas->pluck('namaKelas')->implode(', ') }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">
                            <div class="max-w-xs">
                                @php
                                $allDosen = $item->kelas->flatMap->dosenPengampuKelas->map(function($dosenPengampuKelas) {
                                return $dosenPengampuKelas->dosen;
                                })->unique('id');
                                @endphp
                                @foreach($allDosen->take(3) as $dosen)
                                <div class="text-sm">{{ $dosen->nama }}</div>
                                @endforeach
                                @if($allDosen->count() > 3)
                                <div class="text-xs text-gray-500">+{{ $allDosen->count() - 3 }} dosen lainnya</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">
                            @php
                            $totalMahasiswa = $item->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique()->count();
                            @endphp
                            {{ $totalMahasiswa }} mahasiswa
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.tahun-ajaran-matkul.show', $item->id) }}"
                                    class="inline-flex items-center justify-center bg-gray-600 hover:bg-gray-700 text-white p-1.5 rounded-md" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.tahun-ajaran-matkul.edit', $item->id) }}"
                                    class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white p-1.5 rounded-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button"
                                    data-modal-target="modal-confirm-hapus-{{ $item->id }}"
                                    data-modal-toggle="modal-confirm-hapus-{{ $item->id }}"
                                    class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-md" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-8 text-center text-sm text-gray-500">
                            Tidak ada data tahun ajaran mata kuliah
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Menampilkan {{ $tahunAjaranMatkuls->firstItem() ?? 0 }} sampai {{ $tahunAjaranMatkuls->lastItem() ?? 0 }}
                dari {{ $tahunAjaranMatkuls->total() }} data
            </div>
            <div>
                {{ $tahunAjaranMatkuls->appends(request()->query())->links() }}
            </div>
        </div>

<!-- Modal Konfirmasi Hapus -->
@foreach($tahunAjaranMatkuls as $item)
<x-confirm-modal
    :id="'modal-confirm-hapus-' . $item->id"
    title="Konfirmasi Hapus"
    :message="'Apakah Anda yakin ingin menghapus mata kuliah ' . $item->mataKuliah->namaMatkul . ' dari tahun ajaran ' . $item->tahunAjaran->tahun . '-' . $item->tahunAjaran->periode . '?'"
    :action="route('admin.tahun-ajaran-matkul.destroy', $item->id)"
    method="DELETE" />
@endforeach
