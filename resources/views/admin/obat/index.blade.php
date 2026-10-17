<x-layouts.app title="Manajemen Obat">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Manajemen Obat</h2>
        <a href="{{ route('obat.create') }}" class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-5">
            <i class="fas fa-plus"></i> Tambah Obat
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4 rounded-xl shadow-sm"><i class="fas fa-circle-check"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
        <div class="alert alert-error mb-4 rounded-xl shadow-sm"><i class="fas fa-circle-xmark"></i><span>{{ session('error') }}</span></div>
    @endif
    @if($errors->any())
        <div class="alert alert-error mb-4 rounded-xl shadow-sm"><i class="fas fa-circle-exclamation"></i><span>Periksa kembali data obat yang dimasukkan.</span></div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr><th class="px-6 py-4">Nomor</th><th class="px-6 py-4">Nama Obat</th><th class="px-6 py-4">Kemasan</th><th class="px-6 py-4">Harga</th><th class="px-6 py-4 text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($obats as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 text-slate-500">{{ $obats->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $item->nama_obat }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $item->kemasan ?: '-' }}</td>
                                <td class="px-6 py-4 text-slate-600">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('obat.edit', $item->id) }}" class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-4"><i class="fas fa-pen-to-square"></i> Edit</a>
                                        <form action="{{ route('obat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus obat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-none rounded-lg px-4"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-14 text-slate-400"><i class="fas fa-inbox text-3xl mb-3 block"></i>Belum ada data obat</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($obats->hasPages())
                <div class="px-6 py-4">{{ $obats->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>
