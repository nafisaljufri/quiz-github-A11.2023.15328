@php
    $userNotif = auth()->user();
    $roleNotif = $userNotif->role;
    $jumlahBelumDibaca = $userNotif->unreadNotifications()->count();
    $notifTerbaru = $userNotif->notifications()->limit(5)->get();
@endphp

<div class="dropdown dropdown-end">

    {{-- Ikon lonceng + jumlah belum dibaca --}}
    <div tabindex="0" role="button" class="btn btn-ghost btn-square">
        <span class="indicator">
            @if($jumlahBelumDibaca > 0)
                <span class="indicator-item badge badge-error badge-xs text-white">
                    {{ $jumlahBelumDibaca > 9 ? '9+' : $jumlahBelumDibaca }}
                </span>
            @endif
            <i class="fas fa-bell text-lg"></i>
        </span>
    </div>

    {{-- Dropdown daftar notifikasi --}}
    <div tabindex="0" class="dropdown-content z-50 mt-3 w-80 rounded-xl border border-slate-200 bg-base-100 shadow-xl">

        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <span class="font-bold text-slate-800">Notifikasi</span>
            @if($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route($roleNotif . '.notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:underline">Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($notifTerbaru as $n)
                <div class="border-b border-slate-100 px-4 py-3 text-sm {{ $n->read_at ? 'bg-white text-slate-400' : 'border-l-4 border-l-indigo-500 bg-indigo-50 font-medium text-slate-800' }}">
                    <p>{{ $n->data['pesan'] ?? '-' }}</p>
                    <div class="mt-1 flex items-center justify-between text-xs">
                        <span class="text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
                        @unless($n->read_at)
                            <form method="POST" action="{{ route($roleNotif . '.notifikasi.baca', $n->id) }}">
                                @csrf
                                <button type="submit" class="text-indigo-600 hover:underline">Tandai dibaca</button>
                            </form>
                        @endunless
                    </div>
                </div>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-400">Belum ada notifikasi.</p>
            @endforelse
        </div>

        <a href="{{ route($roleNotif . '.notifikasi') }}"
           class="block border-t border-slate-100 px-4 py-3 text-center text-sm font-semibold text-indigo-600 hover:bg-slate-50">
            Lihat semua notifikasi
        </a>
    </div>
</div>