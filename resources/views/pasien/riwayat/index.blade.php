<x-layouts.app title="Riwayat Pendaftaran">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Riwayat Pendaftaran Poli</h2>
        <p class="mt-1 text-sm text-slate-500">Seluruh pendaftaran periksa yang pernah Anda lakukan.</p>
    </div>

    <div class="card bg-base-100 rounded-2xl border border-slate-200 shadow-md">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-xs uppercase text-slate-500">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th class="text-center">No. Antrian</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayats as $riwayat)
                            @php
                                $jadwal = $riwayat->jadwalPeriksa;
                                $sudahDiperiksa = (bool) $riwayat->periksa;
                                $kedaluwarsa = ! $sudahDiperiksa && $riwayat->tanggal && $riwayat->tanggal->lt(today());
                            @endphp
                            <tr>
                                <td>{{ $riwayats->firstItem() + $loop->index }}</td>
                                <td>
                                    {{ $riwayat->tanggal?->format('d M Y') ?? '-' }}
                                    <div class="text-xs text-slate-400">{{ $jadwal->hari ?? '' }}</div>
                                </td>
                                <td>{{ $jadwal->dokter->poli->nama_poli ?? '-' }}</td>
                                <td>{{ $jadwal->dokter->nama ?? '-' }}</td>
                                <td class="text-center font-bold">{{ $riwayat->no_antrian }}</td>
                                <td>
                                    @if($sudahDiperiksa)
                                        <span class="badge badge-success text-white">Sudah Diperiksa</span>
                                    @elseif($kedaluwarsa)
                                        <span class="badge badge-ghost">Tidak Diperiksa</span>
                                    @else
                                        <span class="badge badge-warning">Menunggu</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($sudahDiperiksa)
                                        <a href="{{ route('pasien.riwayat.show', $riwayat->id) }}"
                                           class="btn btn-sm bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none">
                                            Detail
                                        </a>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    Anda belum pernah mendaftar periksa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($riwayats->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $riwayats->links() }}
                </div>
            @endif
        </div>
    </div>

</x-layouts.app>