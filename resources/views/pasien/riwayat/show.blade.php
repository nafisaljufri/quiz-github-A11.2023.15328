<x-layouts.app title="Detail Pemeriksaan">

    @php
    $periksa = $daftar->periksa;
    $jadwal = $daftar->jadwalPeriksa;
    $totalObat = $periksa->detailPeriksas->sum(fn ($d) => $d->obat->harga ?? 0);
    $biayaJasa = $periksa->biaya_periksa - $totalObat;
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Pemeriksaan</h2>
            <p class="mt-1 text-sm text-slate-500">
                Diperiksa pada {{ \Illuminate\Support\Carbon::parse($periksa->tgl_periksa)->format('d M Y, H:i') }}
            </p>
        </div>
        <a href="{{ route('pasien.riwayat') }}" class="btn btn-ghost rounded-lg">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- INFORMASI + CATATAN --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="card bg-base-100 rounded-2xl border border-slate-200 shadow-md">
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <p class="text-xs text-slate-400">Poli</p>
                            <p class="font-semibold">{{ $jadwal->dokter->poli->nama_poli ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Dokter</p>
                            <p class="font-semibold">{{ $jadwal->dokter->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Tanggal Jadwal</p>
                            <p class="font-semibold">{{ $daftar->tanggal?->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">No. Antrian</p>
                            <p class="font-semibold">{{ $daftar->no_antrian }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 rounded-2xl border border-slate-200 shadow-md">
                <div class="card-body">
                    <h3 class="font-bold text-slate-800">Keluhan</h3>
                    <p class="text-slate-600">{{ $daftar->keluhan ?: '-' }}</p>

                    <h3 class="mt-4 font-bold text-slate-800">Catatan Dokter</h3>
                    <p class="whitespace-pre-line text-slate-600">{{ $periksa->catatan ?: 'Tidak ada catatan.' }}</p>
                </div>
            </div>
        </div>

        {{-- OBAT + BIAYA --}}
        <div class="card bg-base-100 h-fit rounded-2xl border border-slate-200 shadow-md">
            <div class="card-body">
                <h3 class="font-bold text-slate-800">Daftar Obat</h3>

                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse($periksa->detailPeriksas as $detail)
                    <li class="flex justify-between py-2">
                        <span>
                            {{ $detail->obat->nama_obat ?? '-' }}
                            <span class="text-xs text-slate-400">{{ $detail->obat->kemasan ?? '' }}</span>
                        </span>
                        <span>Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}</span>
                    </li>
                    @empty
                    <li class="py-2 text-slate-400">Tidak ada obat.</li>
                    @endforelse
                </ul>

                <div class="mt-3 space-y-1 border-t border-slate-200 pt-3 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Total obat</span>
                        <span>Rp {{ number_format($totalObat, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Jasa periksa</span>
                        <span>Rp {{ number_format($biayaJasa, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-1 text-base font-bold text-slate-800">
                        <span>Total Biaya</span>
                        <span>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('pasien.pembayaran.show', $periksa->id) }}"
                        class="btn btn-sm mt-3 w-full bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none">
                        <i class="fas fa-money-bill-wave"></i> Lihat Pembayaran
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>