@push('scripts')
<script>
(() => {
    const select = document.getElementById('pilihObat');
    const rows = document.getElementById('daftarObat');
    const hidden = document.getElementById('obatJson');
    const total = document.getElementById('totalHarga');
    const status = document.getElementById('statusObat');
    const currency = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
    const obatTersedia = new Map(Array.from(select.options).filter(option => option.value).map(option => [option.value, {
        id: option.value,
        nama: option.dataset.name,
        harga: Number(option.dataset.price)
    }]));
    let terpilih = [];

    function render() {
        rows.replaceChildren();
        if (terpilih.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 3;
            cell.className = 'text-center py-6 text-slate-400';
            cell.textContent = 'Belum ada obat dipilih.';
            row.append(cell);
            rows.append(row);
        }
        terpilih.forEach(obat => {
            const row = document.createElement('tr');
            const name = document.createElement('td');
            name.className = 'px-4 py-3 font-semibold text-slate-800';
            name.textContent = obat.nama;
            const price = document.createElement('td');
            price.className = 'px-4 py-3 text-slate-600';
            price.textContent = currency.format(obat.harga);
            const action = document.createElement('td');
            action.className = 'px-4 py-3 text-right';
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm btn-error btn-outline rounded-lg';
            remove.textContent = 'Hapus';
            remove.setAttribute('aria-label', `Hapus ${obat.nama}`);
            remove.addEventListener('click', () => {
                terpilih = terpilih.filter(item => item.id !== obat.id);
                status.textContent = '';
                render();
            });
            action.append(remove);
            row.append(name, price, action);
            rows.append(row);
        });
        hidden.value = JSON.stringify(terpilih.map(obat => Number(obat.id)));
        const totalObat = terpilih.reduce((sum, obat) => sum + obat.harga, 0);
        total.textContent = currency.format(totalObat);
    }

    document.getElementById('tambahObat').addEventListener('click', () => {
        const obat = obatTersedia.get(select.value);
        if (!obat) {
            status.textContent = 'Pilih obat dari daftar terlebih dahulu.';
            return;
        }
        if (terpilih.some(item => item.id === obat.id)) {
            status.textContent = `${obat.nama} sudah ada di daftar obat terpilih.`;
            return;
        }
        terpilih.push(obat);
        status.textContent = '';
        render();
        select.value = '';
    });

    try {
        const lama = JSON.parse(hidden.value);
        if (Array.isArray(lama)) terpilih = lama.map(id => obatTersedia.get(String(id))).filter(Boolean);
    } catch (_) {
        terpilih = [];
    }
    render();
    document.getElementById('pemeriksaanForm').addEventListener('submit', event => {
        if (terpilih.length === 0) {
            event.preventDefault();
            alert('Pilih setidaknya satu obat sebelum menyimpan pemeriksaan.');
        }
    });
})();
</script>
@endpush
<x-layouts.app title="Periksa Pasien">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition" aria-label="Kembali ke antrean">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Catat Pemeriksaan</h2>
            <p class="text-sm text-slate-500">Lengkapi catatan pemeriksaan dan obat pasien.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error mb-4 rounded-xl shadow-sm" role="alert">
            <i class="fas fa-circle-exclamation"></i>
            <div><p class="font-semibold">Periksa kembali data berikut:</p><ul class="list-disc ml-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        </div>
    @endif

    <form id="pemeriksaanForm" action="{{ route('periksa-pasien.store') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="id_daftar_poli" value="{{ $daftarPasien->id }}">
        <input type="hidden" name="obat_json" id="obatJson" value="{{ old('obat_json', '[]') }}">

        <section class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
            <div class="card-body p-0">
                <div class="px-6 py-4 border-b border-slate-200"><h3 class="font-bold text-slate-800">Informasi Pasien</h3></div>
                <div class="px-6 py-5 grid gap-3 sm:grid-cols-2 text-sm">
                    <div><span class="text-slate-500">Nama Pasien</span><p class="font-semibold text-slate-800">{{ $daftarPasien->pasien->nama ?? 'Pasien tidak tersedia' }}</p></div>
                    <div><span class="text-slate-500">Nomor Antrean</span><p class="font-semibold text-slate-800">{{ $daftarPasien->no_antrian }}</p></div>
                    <div class="sm:col-span-2"><span class="text-slate-500">Keluhan</span><p class="text-slate-800">{{ $daftarPasien->keluhan }}</p></div>
                </div>
            </div>
        </section>

        <section class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
            <div class="card-body gap-5">
                <div>
                    <label for="pilihObat" class="label"><span class="label-text font-semibold">Pilih Obat</span></label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select id="pilihObat" class="select select-bordered w-full">
                            <option value="">Pilih obat dari daftar</option>
                            @foreach($obats as $obat)
                                <option value="{{ $obat->id }}" data-name="{{ $obat->nama_obat }}" data-price="{{ $obat->harga }}">{{ $obat->nama_obat }} — Rp {{ number_format($obat->harga, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="tambahObat" class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-5 shrink-0"><i class="fas fa-plus"></i> Tambah Obat</button>
                    </div>
                    <p id="statusObat" class="mt-2 text-sm text-amber-700" role="status" aria-live="polite"></p>
                    @if($obats->isEmpty())<p class="mt-2 text-sm text-amber-700">Belum ada obat yang tersedia. Tambahkan data obat sebelum menyimpan pemeriksaan.</p>@endif
                </div>

                <div>
                    <h3 class="font-bold text-slate-800 mb-3">Obat Terpilih</h3>
                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                        <table class="table table-zebra w-full">
                            <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider"><tr><th class="px-4 py-3">Nama Obat</th><th class="px-4 py-3">Harga Satuan</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
                            <tbody id="daftarObat"><tr id="obatKosong"><td colspan="3" class="text-center py-6 text-slate-400">Belum ada obat dipilih.</td></tr></tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-xl bg-indigo-50 px-5 py-4">
                    <span class="font-bold text-slate-700">Total Harga Obat</span>
                    <span id="totalHarga" class="text-xl font-bold text-[#2d4499]">Rp 0</span>
                </div>

                <div>
                    <label for="catatan" class="label"><span class="label-text font-semibold">Catatan <span class="font-normal text-slate-400">(opsional)</span></span></label>
                    <textarea id="catatan" name="catatan" rows="4" class="textarea textarea-bordered w-full" placeholder="Tuliskan hasil pemeriksaan...">{{ old('catatan') }}</textarea>
                    @error('catatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                    <a href="{{ route('periksa-pasien.index') }}" class="btn btn-ghost rounded-lg">Batal</a>
                    <button type="submit" class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-6"><i class="fas fa-floppy-disk"></i> Simpan Pemeriksaan</button>
                </div>
            </div>
        </section>
    </form>
</x-layouts.app>
