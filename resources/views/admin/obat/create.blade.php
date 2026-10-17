<x-layouts.app title="Tambah Obat">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('obat.index') }}" class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200"><i class="fas fa-arrow-left text-xs"></i></a>
        <h2 class="text-2xl font-bold text-slate-800">Tambah Obat</h2>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-7">
            <form action="{{ route('obat.store') }}" method="POST">
                @csrf
                <div class="form-control mb-5">
                    <label for="nama_obat" class="label pb-1"><span class="text-sm font-semibold text-gray-700">Nama Obat <span class="text-red-500">*</span></span></label>
                    <input id="nama_obat" type="text" name="nama_obat" value="{{ old('nama_obat') }}" maxlength="255" required class="input input-bordered w-full rounded-lg text-sm @error('nama_obat') input-error @enderror">
                    @error('nama_obat')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="form-control mb-5">
                    <label for="kemasan" class="label pb-1"><span class="text-sm font-semibold text-gray-700">Kemasan</span></label>
                    <input id="kemasan" type="text" name="kemasan" value="{{ old('kemasan') }}" maxlength="35" class="input input-bordered w-full rounded-lg text-sm @error('kemasan') input-error @enderror">
                    @error('kemasan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="form-control mb-6">
                    <label for="harga" class="label pb-1"><span class="text-sm font-semibold text-gray-700">Harga (Rp) <span class="text-red-500">*</span></span></label>
                    <input id="harga" type="number" name="harga" value="{{ old('harga') }}" min="0" step="1" required class="input input-bordered w-full rounded-lg text-sm @error('harga') input-error @enderror">
                    @error('harga')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-lg text-sm font-semibold"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('obat.index') }}" class="flex items-center gap-2 px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-semibold">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
