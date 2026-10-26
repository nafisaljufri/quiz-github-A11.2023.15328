<x-layouts.app title="Pendaftaran Periksa">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Pendaftaran Periksa</h2>
        <p class="mt-1 text-sm text-slate-500">Pilih poli dan jadwal dokter untuk mengambil nomor antrean.</p>
    </div>

    @if(session('message'))
        <div class="alert {{ session('type') === 'success' ? 'alert-success' : 'alert-info' }} mb-4 rounded-xl shadow-sm">
            <i class="fas fa-circle-check"></i><span>{{ session('message') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error mb-4 rounded-xl shadow-sm" role="alert">
            <i class="fas fa-circle-exclamation"></i>
            <div>
                <p class="font-semibold">Pendaftaran belum dapat dikirim:</p>
                <ul class="list-disc ml-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-6 sm:p-8">
            <div class="mb-6 rounded-xl bg-indigo-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pasien</p>
                <p class="mt-1 font-bold text-slate-800">{{ $user->nama }}</p>
            </div>

            <form action="{{ route('pasien.daftar.submit') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                <div>
                    <label for="id_poli" class="label"><span class="label-text font-semibold">Poli</span></label>
                    <select id="id_poli" name="id_poli" class="select select-bordered w-full @error('id_poli') select-error @enderror" required>
                        <option value="" @selected(!old('id_poli')) disabled hidden>Pilih poli</option>
                        @foreach($polis as $poli)
                            <option value="{{ $poli->id }}" @selected((string) old('id_poli') === (string) $poli->id)>{{ $poli->nama_poli }}</option>
                        @endforeach
                    </select>
                    @error('id_poli')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @if($polis->isEmpty())<p class="mt-1 text-sm text-amber-700">Belum ada poli yang tersedia untuk pendaftaran.</p>@endif
                </div>

                <div>
                    <label for="id_jadwal" class="label"><span class="label-text font-semibold">Jadwal Dokter</span></label>
                    <select id="id_jadwal" name="id_jadwal" class="select select-bordered w-full @error('id_jadwal') select-error @enderror" required>
                        <option id="placeholderJadwal" value="" @selected(!old('id_jadwal')) disabled hidden>Pilih poli terlebih dahulu</option>
                        @foreach($jadwals as $jadwal)
                            @if($jadwal->dokter)
                                <option value="{{ $jadwal->id }}" data-poli="{{ $jadwal->dokter->id_poli }}" @selected((string) old('id_jadwal') === (string) $jadwal->id)>
                                    {{ $jadwal->dokter->nama }} — {{ $jadwal->hari }}, {{ \Illuminate\Support\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}–{{ \Illuminate\Support\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('id_jadwal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p id="statusJadwal" class="mt-1 text-sm text-amber-700" aria-live="polite"></p>
                </div>

                <div>
                    <label for="keluhan" class="label"><span class="label-text font-semibold">Keluhan <span class="font-normal text-slate-400">(opsional)</span></span></label>
                    <textarea id="keluhan" name="keluhan" rows="4" class="textarea textarea-bordered w-full @error('keluhan') textarea-error @enderror" placeholder="Jelaskan keluhan yang dirasakan...">{{ old('keluhan') }}</textarea>
                    @error('keluhan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                    <a href="{{ route('pasien.dashboard') }}" class="btn btn-ghost rounded-lg">Batal</a>
                    <button type="submit" class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-6" @disabled($polis->isEmpty() || $jadwals->isEmpty())>
                        <i class="fas fa-calendar-check"></i> Daftar Antrean
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

@push('scripts')
<script>
(() => {
    const poli = document.getElementById('id_poli');
    const jadwal = document.getElementById('id_jadwal');
    const placeholder = document.getElementById('placeholderJadwal');
    const status = document.getElementById('statusJadwal');
    const jadwalOptions = Array.from(jadwal.options).filter(option => option.value);
    const oldJadwal = @json((string) old('id_jadwal', ''));

    function filterJadwal() {
        const selectedPoli = poli.value;
        let jumlahTersedia = 0;
        jadwalOptions.forEach(option => {
            const cocok = Boolean(selectedPoli) && option.dataset.poli === selectedPoli;
            option.hidden = !cocok;
            option.disabled = !cocok;
            if (cocok) jumlahTersedia++;
        });

        if (!selectedPoli) {
            placeholder.textContent = 'Pilih poli terlebih dahulu';
            status.textContent = jadwalOptions.length === 0
                ? 'Belum ada jadwal dokter yang tersedia.'
                : 'Pilih poli untuk melihat jadwal dokter yang tersedia.';
        } else if (jumlahTersedia === 0) {
            placeholder.textContent = 'Tidak ada jadwal dokter untuk poli ini';
            status.textContent = 'Belum ada jadwal dokter untuk poli yang dipilih.';
        } else {
            placeholder.textContent = 'Pilih jadwal dokter';
            status.textContent = '';
        }
        const selected = jadwal.selectedOptions[0];
        if (selected && selected.value && selected.hidden) jadwal.value = '';
        if (selectedPoli && oldJadwal && jadwalOptions.some(option => option.value === oldJadwal && option.dataset.poli === selectedPoli)) {
            jadwal.value = oldJadwal;
        }
    }

    poli.addEventListener('change', filterJadwal);
    filterJadwal();
})();
</script>
@endpush
