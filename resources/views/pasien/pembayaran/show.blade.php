<x-layouts.app title="Detail Tagihan">

    @php
        $jadwal = $periksa->daftarPoli->jadwalPeriksa;
        $totalObat = $periksa->detailPeriksas->sum(fn ($d) => $d->obat->harga ?? 0);
        $biayaJasa = $periksa->biaya_periksa - $totalObat;
        $lunas = $periksa->status_bayar === 'lunas';
        $badge = match($periksa->status_bayar) {
            'lunas' => ['badge-success text-white', 'Lunas'],
            'menunggu_verifikasi' => ['badge-warning', 'Menunggu Verifikasi'],
            default => ['badge-error text-white', 'Belum Dibayar'],
        };
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Detail Tagihan</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $jadwal->dokter->poli->nama_poli ?? '-' }} &middot; {{ $jadwal->dokter->nama ?? '-' }}
                &middot; {{ \Illuminate\Support\Carbon::parse($periksa->tgl_periksa)->format('d M Y') }}
            </p>
        </div>
        <a href="{{ route('pasien.pembayaran') }}" class="btn btn-ghost rounded-lg">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- RINCIAN TAGIHAN --}}
        <div class="card bg-base-100 h-fit rounded-2xl border border-slate-200 shadow-md">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Rincian Tagihan</h3>
                    <span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                </div>

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

                @if($lunas)
                    <p class="mt-3 text-sm text-emerald-600">
                        <i class="fas fa-circle-check"></i>
                        Dinyatakan lunas pada {{ $periksa->tgl_lunas?->format('d M Y, H:i') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- UPLOAD BUKTI --}}
        <div class="card bg-base-100 h-fit rounded-2xl border border-slate-200 shadow-md">
            <div class="card-body">
                <h3 class="font-bold text-slate-800">Bukti Pembayaran</h3>

                @if($periksa->bukti_url)
                    <a href="{{ $periksa->bukti_url }}" target="_blank">
                        <img src="{{ $periksa->bukti_url }}" alt="Bukti pembayaran"
                             class="max-h-72 w-full rounded-xl border border-slate-200 object-contain">
                    </a>
                    <p class="text-xs text-slate-400">
                        Diupload {{ $periksa->tgl_upload_bukti?->format('d M Y, H:i') }}
                    </p>
                @else
                    <p class="text-sm text-slate-400">Belum ada bukti pembayaran yang diupload.</p>
                @endif

                @unless($lunas)
                    <form action="{{ route('pasien.pembayaran.upload', $periksa->id) }}" method="POST"
                          enctype="multipart/form-data" class="mt-3 space-y-3">
                        @csrf
                        <input type="file" name="bukti_bayar" accept="image/png,image/jpeg"
                               class="file-input file-input-bordered w-full @error('bukti_bayar') file-input-error @enderror">
                        @error('bukti_bayar')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="btn w-full bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none">
                            <i class="fas fa-upload"></i>
                            {{ $periksa->bukti_bayar ? 'Upload Ulang Bukti' : 'Upload Bukti Pembayaran' }}
                        </button>
                    </form>
                @endunless
            </div>
        </div>

    </div>
</x-layouts.app>