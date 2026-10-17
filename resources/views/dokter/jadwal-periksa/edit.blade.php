<x-layouts.app title="Edit Jadwal">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('jadwal-periksa.index') }}" class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200"><i class="fas fa-arrow-left text-xs"></i></a>
        <h2 class="text-2xl font-bold text-slate-800">Edit Jadwal Periksa</h2>
    </div>
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-7">
            <form action="{{ route('jadwal-periksa.update', $jadwalPeriksa->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-control mb-5">
                    <label for="hari" class="label pb-1"><span class="text-sm font-semibold text-gray-700">Hari <span class="text-red-500">*</span></span></label>
                    <select id="hari" name="hari" required class="select select-bordered w-full rounded-lg text-sm @error('hari') select-error @enderror">
                        <option value="">Pilih hari</option>
                        @foreach($hariList as $hari)<option value="{{ $hari }}" @selected(old('hari', $jadwalPeriksa->hari) === $hari)>{{ $hari }}</option>@endforeach
                    </select>
                    @error('hari')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="form-control mb-5">
                    <label for="jam_mulai" class="label pb-1"><span class="text-sm font-semibold text-gray-700">Jam Mulai <span class="text-red-500">*</span></span></label>
                    <input id="jam_mulai" type="time" name="jam_mulai" value="{{ old('jam_mulai', \Illuminate\Support\Carbon::parse($jadwalPeriksa->jam_mulai)->format('H:i')) }}" required class="input input-bordered w-full rounded-lg text-sm @error('jam_mulai') input-error @enderror">
                    @error('jam_mulai')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="form-control mb-6">
                    <label for="jam_selesai" class="label pb-1"><span class="text-sm font-semibold text-gray-700">Jam Selesai <span class="text-red-500">*</span></span></label>
                    <input id="jam_selesai" type="time" name="jam_selesai" value="{{ old('jam_selesai', \Illuminate\Support\Carbon::parse($jadwalPeriksa->jam_selesai)->format('H:i')) }}" required class="input input-bordered w-full rounded-lg text-sm @error('jam_selesai') input-error @enderror">
                    @error('jam_selesai')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#2d4499] hover:bg-[#1e2d6b] text-white rounded-lg text-sm font-semibold"><i class="fas fa-save"></i> Perbarui</button>
                    <a href="{{ route('jadwal-periksa.index') }}" class="flex items-center gap-2 px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-semibold">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
