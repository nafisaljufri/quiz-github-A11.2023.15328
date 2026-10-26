<x-layouts.app title="Dashboard Pasien">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Halo, {{ auth()->user()->nama }}</h2>
        <p class="mt-1 text-sm text-slate-500">Pantau antrian dan jadwal poliklinik Anda di sini.</p>
    </div>

    {{-- ============ BANNER ANTRIAN AKTIF ============ --}}
    @if($antrianAktif)
        @php
            $j = $antrianAktif->jadwalPeriksa;
            $mulai = \Illuminate\Support\Carbon::parse($j->jam_mulai)->format('H:i');
            $selesai = \Illuminate\Support\Carbon::parse($j->jam_selesai)->format('H:i');
        @endphp

        <div class="mb-8 rounded-2xl bg-gradient-to-r from-indigo-700 to-blue-500 p-6 text-white shadow-lg">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                <div>
                    <p class="mb-4 text-xs font-bold uppercase tracking-widest text-white/80">Antrian Aktif Anda</p>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-xs text-white/70">Poliklinik</p>
                            <p class="text-lg font-bold">{{ $j->dokter->poli->nama_poli ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-white/70">Dokter</p>
                            <p class="text-lg font-bold">{{ $j->dokter->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-white/70">Jadwal Periksa</p>
                            <p class="text-lg font-bold">{{ $j->hari }} ({{ $mulai }} - {{ $selesai }})</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="rounded-xl bg-white/20 px-6 py-4 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-white/80">Nomor Anda</p>
                        <p class="text-4xl font-extrabold">{{ $antrianAktif->no_antrian }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-6 py-4 text-center text-indigo-700">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Sedang Dilayani</p>
                        <p class="text-4xl font-extrabold" data-sedang-dilayani="{{ $j->id }}">
                            {{ $sedangDilayani[$j->id] ?? '-' }}
                        </p>
                        <p class="mt-1 text-[10px] text-emerald-600">&bull; Live update</p>
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="mb-8 rounded-2xl border border-dashed border-slate-300 bg-base-100 p-6 text-center text-slate-500">
            <i class="fas fa-ticket mb-2 text-2xl"></i>
            <p class="font-semibold">Anda belum memiliki antrian aktif.</p>
            <a href="{{ route('pasien.daftar') }}" class="mt-3 btn btn-sm bg-[#2d4499] text-white border-none">
                Daftar Periksa Sekarang
            </a>
        </div>
    @endif

    {{-- ============ TABEL JADWAL ============ --}}
    <div class="card bg-base-100 rounded-2xl border border-slate-200 shadow-md">
        <div class="card-body p-0">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-bold text-slate-800">Jadwal Poliklinik & Antrian</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="text-xs uppercase text-slate-500">
                            <th>No</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Hari</th>
                            <th>Jam Periksa</th>
                            <th class="text-center">Sedang Dilayani</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $jadwal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $jadwal->dokter->poli->nama_poli ?? '-' }}</td>
                                <td>{{ $jadwal->dokter->nama ?? '-' }}</td>
                                <td>{{ $jadwal->hari }}</td>
                                <td>
                                    {{ \Illuminate\Support\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                    -
                                    {{ \Illuminate\Support\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-lg badge-primary font-bold"
                                          data-sedang-dilayani="{{ $jadwal->id }}">
                                        {{ $sedangDilayani[$jadwal->id] ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada jadwal poliklinik.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>