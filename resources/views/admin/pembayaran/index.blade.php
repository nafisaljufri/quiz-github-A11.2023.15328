<x-layouts.app title="Verifikasi Pembayaran">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Verifikasi Pembayaran</h2>
        <p class="mt-1 text-sm text-slate-500">Periksa bukti bayar pasien lalu konfirmasi pembayaran.</p>
    </div>

    <div role="tablist" class="tabs tabs-boxed mb-4 w-fit">
        <a role="tab" href="{{ route('admin.pembayaran.index', ['status' => 'menunggu_verifikasi']) }}"
           class="tab {{ $status === 'menunggu_verifikasi' ? 'tab-active' : '' }}">Menunggu Verifikasi</a>
        <a role="tab" href="{{ route('admin.pembayaran.index', ['status' => 'lunas']) }}"
           class="tab {{ $status === 'lunas' ? 'tab-active' : '' }}">Lunas</a>
        <a role="tab" href="{{ route('admin.pembayaran.index', ['status' => 'belum_bayar']) }}"
           class="tab {{ $status === 'belum_bayar' ? 'tab-active' : '' }}">Belum Dibayar</a>
    </div>

    <div class="card bg-base-100 rounded-2xl border border-slate-200 shadow-md">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-xs uppercase text-slate-500">
                            <th>No</th>
                            <th>Pasien</th>
                            <th>Poli / Dokter</th>
                            <th>Tgl Periksa</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihans as $t)
                            @php $jadwal = $t->daftarPoli->jadwalPeriksa; @endphp
                            <tr>
                                <td>{{ $tagihans->firstItem() + $loop->index }}</td>
                                <td class="font-semibold">{{ $t->daftarPoli->pasien->nama ?? '-' }}</td>
                                <td>
                                    {{ $jadwal->dokter->poli->nama_poli ?? '-' }}
                                    <div class="text-xs text-slate-400">{{ $jadwal->dokter->nama ?? '-' }}</div>
                                </td>
                                <td>{{ \Illuminate\Support\Carbon::parse($t->tgl_periksa)->format('d M Y') }}</td>
                                <td class="text-right font-semibold">Rp {{ number_format($t->biaya_periksa, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.pembayaran.show', $t->id) }}"
                                       class="btn btn-sm bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none">
                                        {{ $status === 'menunggu_verifikasi' ? 'Periksa Bukti' : 'Detail' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">Tidak ada data.</td>
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