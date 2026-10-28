<x-layouts.app title="Notifikasi">

    @php $role = auth()->user()->role; @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Notifikasi</h2>
            <p class="mt-1 text-sm text-slate-500">Pemberitahuan terbaru untuk akun Anda.</p>
        </div>

        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route($role . '.notifikasi.baca-semua') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline">
                    <i class="fas fa-check-double"></i> Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="card bg-base-100 rounded-2xl border border-slate-200 shadow-md">
        <div class="card-body p-0">

            @forelse($notifikasis as $n)
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-4
                            {{ $n->read_at ? 'text-slate-400' : 'border-l-4 border-l-indigo-500 bg-indigo-50 text-slate-800' }}">
                    <div>
                        <p class="{{ $n->read_at ? '' : 'font-semibold' }}">{{ $n->data['pesan'] ?? '-' }}</p>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $n->created_at->format('d M Y, H:i') }} &middot; {{ $n->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        @if(!empty($n->data['url']))
                            <a href="{{ $n->data['url'] }}" class="btn btn-xs btn-ghost">Lihat</a>
                        @endif

                        @if($n->read_at)
                            <span class="badge badge-ghost badge-sm">Dibaca</span>
                        @else
                            <form method="POST" action="{{ route($role . '.notifikasi.baca', $n->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs bg-[#2d4499] text-white border-none">
                                    Tandai dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <i class="fas fa-bell-slash mb-2 text-2xl"></i>
                    <p>Belum ada notifikasi.</p>
                </div>
            @endforelse

            @if($notifikasis->hasPages())
                <div class="px-6 py-4">{{ $notifikasis->links() }}</div>
            @endif
        </div>
    </div>

</x-layouts.app>