<x-layouts.app title="Pembayaran">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Pembayaran</h2>
        <p class="mt-1 text-sm text-slate-500">Tagihan dari hasil pemeriksaan Anda.</p>
    </div>

    <div class="card bg-base-100 rounded-2xl border border-slate-200 shadow-md">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-xs uppercase text-slate-500">
                            <th>No</th>
                            <th>Tanggal Periksa</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th class="text-right">Total Tagihan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihans as $t)
                            @php
                                $jadwal = $t->daftarPoli->jadwalPeriksa;
                                $badge = match($t->status_bayar) {
                                    'lunas' => ['badge-success text-white', 'Lunas'],
                                    'menunggu_verifikasi' => ['badge-warning', 'Menunggu Verifikasi'],
                                    default => ['badge-error text-white', 'Belum Dibayar'],
                                };
                            @endphp
                            <tr>
                                <td>{{ $tagihans->firstItem() + $loop->index }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($t->tgl_periksa)->format('d M Y') }}</td>
                                <td>{{ $jadwal->dokter->poli->nama_poli ?? '-' }}</td>
                                <td>{{ $jadwal->dokter->nama ?? '-' }}</td>
                                <td class="text-right font-semibold">Rp {{ number_format($t->biaya_periksa, 0, ',', '.') }}</td>
                                <td><span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('pasien.pembayaran.show', $t->id) }}"
                                       class="btn btn-sm bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none">
                                        {{ $t->status_bayar === 'belum_bayar' ? 'Bayar' : 'Lihat' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">Belum ada tagihan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tagihans->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $tagihans->links() }}</div>
            @endif
        </div>
    </div>

</x-layouts.app>