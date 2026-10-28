<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        // Hanya notifikasi milik user yang login, terbaru di atas
        $notifikasis = Auth::user()->notifications()->paginate(10);

        return view('notifikasi.index', compact('notifikasis'));
    }

    public function baca($id)
    {
        // Dicari lewat relasi milik user: ID milik orang lain => 404
        $notifikasi = Auth::user()->notifications()->findOrFail($id);
        $notifikasi->markAsRead();

        return back();
    }

    public function bacaSemua()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}