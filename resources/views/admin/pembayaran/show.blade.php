<x-layouts.app title="Detail Pembayaran">

    @php
        $jadwal = $periksa->daftarPoli->jadwalPeriksa;
        $totalObat = $periksa->detailPeriksas->sum(fn ($d) => $d->obat->harga ?? 0);
        $biayaJasa = $periksa->biaya_periksa - $totalObat;
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Pembayaran</h2>
            <p class="mt-1 text-sm text-slate-500">
                Pasien: <span class="font-semibold">{{ $periksa->daftarPoli->pasien->nama ?? '-' }}</span>
                &middot; {{ $jadwal->dokter->poli->nama_poli ?? '-' }}
            </p>
        </div>
        <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-ghost rounded-lg">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">

        <div class="card bg-base-100 h-fit rounded-2xl border border-slate-200 shadow-md">
            <div class="card-body">
                <h3 class="font-bold text-slate-800">Rincian Tagihan</h3>
                <ul class="divide-y divide-slate-100 text-sm">
                    @foreach($periksa->detailPeriksas as $detail)
                        <li class="flex justify-between py-2">
                            <span>{{ $detail->obat->nama_obat ?? '-' }}</span>
                            <span>Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                    <li class="flex justify-between py-2">
                        <span>Jasa periksa</span>
                        <span>Rp {{ number_format($biayaJasa, 0, ',', '.') }}</span>
                    </li>
                </ul>
                <div class="mt-2 flex justify-between border-t border-slate-200 pt-3 text-base font-bold text-slate-800">
                    <span>Total</span>
                    <span>Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 h-fit rounded-2xl border border-slate-200 shadow-md">
            <div class="card-body">
                <h3 class="font-bold text-slate-800">Bukti Pembayaran</h3>

                @if($periksa->bukti_url)
                    <a href="{{ $periksa->bukti_url }}" target="_blank">
                        <img src="{{ $periksa->bukti_url }}" alt="Bukti pembayaran"
                             class="max-h-80 w-full rounded-xl border border-slate-200 object-contain">
                    </a>
                    <p class="text-xs text-slate-400">
                        Diupload {{ $periksa->tgl_upload_bukti?->format('d M Y, H:i') }}
                    </p>
                @else
                    <p class="text-sm text-slate-400">Pasien belum mengupload bukti pembayaran.</p>
                @endif

                @if($periksa->status_bayar === 'menunggu_verifikasi')
                    <form action="{{ route('admin.pembayaran.konfirmasi', $periksa->id) }}" method="POST"
                          onsubmit="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')">
                        @csrf
                        <button type="submit" class="btn mt-3 w-full btn-success text-white">
                            <i class="fas fa-circle-check"></i> Konfirmasi Pembayaran (Lunas)
                        </button>
                    </form>
                @elseif($periksa->status_bayar === 'lunas')
                    <div class="mt-3 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        <i class="fas fa-circle-check"></i>
                        Lunas pada {{ $periksa->tgl_lunas?->format('d M Y, H:i') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>