<x-layouts.app title="Jadwal Periksa">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Jadwal Periksa</h2>
        <a href="{{ route('jadwal-periksa.create') }}" class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-5">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4 rounded-xl shadow-sm"><i class="fas fa-circle-check"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
        <div class="alert alert-error mb-4 rounded-xl shadow-sm"><i class="fas fa-circle-xmark"></i><span>{{ session('error') }}</span></div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr><th class="px-6 py-4">Nomor</th><th class="px-6 py-4">Hari</th><th class="px-6 py-4">Jam Mulai</th><th class="px-6 py-4">Jam Selesai</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 text-slate-500">{{ $jadwals->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $item->hari }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ \Illuminate\Support\Carbon::parse($item->jam_mulai)->format('H:i') }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ \Illuminate\Support\Carbon::parse($item->jam_selesai)->format('H:i') }}</td>
                                <td class="px-6 py-4 text-slate-500">Tidak tersedia</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('jadwal-periksa.edit', $item->id) }}" class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-4"><i class="fas fa-pen-to-square"></i> Edit</a>
                                        <form action="{{ route('jadwal-periksa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-none rounded-lg px-4"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-14 text-slate-400"><i class="fas fa-inbox text-3xl mb-3 block"></i>Belum ada jadwal periksa</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($jadwals->hasPages())<div class="px-6 py-4">{{ $jadwals->links() }}</div>@endif
        </div>
    </div>
</x-layouts.app>
