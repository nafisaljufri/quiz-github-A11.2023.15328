<x-layouts.app title="Profile">

    @php $role = $user->role; @endphp

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Profile</h2>
        <p class="mt-1 text-sm text-slate-500">Kelola informasi dan keamanan akun Anda.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- INFORMASI AKUN --}}
        <div class="card bg-base-100 h-fit rounded-2xl border border-slate-200 shadow-md">
            <div class="card-body">
                <h3 class="font-bold text-slate-800">Informasi Akun</h3>

                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="flex justify-between py-3">
                        <dt class="text-slate-500">Nama</dt>
                        <dd class="font-semibold text-slate-800">{{ $user->nama }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-slate-500">Email</dt>
                        <dd class="font-semibold text-slate-800">{{ $user->email }}</dd>
                    </div>
                    <div class="flex justify-between py-3">
                        <dt class="text-slate-500">Role</dt>
                        <dd><span class="badge badge-primary capitalize">{{ $user->role }}</span></dd>
                    </div>
                    @if($user->no_hp)
                        <div class="flex justify-between py-3">
                            <dt class="text-slate-500">No. HP</dt>
                            <dd class="font-semibold text-slate-800">{{ $user->no_hp }}</dd>
                        </div>
                    @endif
                    @if($user->role === 'pasien' && $user->no_rm)
                        <div class="flex justify-between py-3">
                            <dt class="text-slate-500">No. RM</dt>
                            <dd class="font-semibold text-slate-800">{{ $user->no_rm }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- FORM GANTI PASSWORD --}}
        <div class="card bg-base-100 h-fit rounded-2xl border border-slate-200 shadow-md">
            <div class="card-body">
                <h3 class="font-bold text-slate-800">Ganti Password</h3>

                <form action="{{ route('profile.' . $role . '.password') }}" method="POST" class="mt-2 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="password_lama" class="label pb-1">
                            <span class="text-sm font-semibold text-gray-700">Password Lama <span class="text-red-500">*</span></span>
                        </label>
                        <input id="password_lama" type="password" name="password_lama" autocomplete="current-password"
                               class="input input-bordered w-full rounded-lg text-sm @error('password_lama') input-error @enderror">
                        @error('password_lama')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_baru" class="label pb-1">
                            <span class="text-sm font-semibold text-gray-700">Password Baru <span class="text-red-500">*</span></span>
                        </label>
                        <input id="password_baru" type="password" name="password_baru" autocomplete="new-password"
                               class="input input-bordered w-full rounded-lg text-sm @error('password_baru') input-error @enderror">
                        <p class="mt-1 text-xs text-slate-400">Minimal 8 karakter dan berbeda dari password lama.</p>
                        @error('password_baru')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_baru_confirmation" class="label pb-1">
                            <span class="text-sm font-semibold text-gray-700">Konfirmasi Password Baru <span class="text-red-500">*</span></span>
                        </label>
                        <input id="password_baru_confirmation" type="password" name="password_baru_confirmation" autocomplete="new-password"
                               class="input input-bordered w-full rounded-lg text-sm @error('password_baru_confirmation') input-error @enderror">
                        @error('password_baru_confirmation')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="flex items-center gap-2 rounded-lg bg-[#2d4499] px-6 py-2.5 text-sm font-semibold text-white hover:bg-[#1e2d6b]">
                        <i class="fas fa-key"></i> Simpan Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>