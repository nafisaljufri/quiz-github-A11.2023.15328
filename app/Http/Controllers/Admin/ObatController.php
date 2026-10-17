<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ObatController extends Controller
{
    public function index()
    {
        $obats = Obat::latest()->paginate(10);

        return view('admin.obat.index', compact('obats'));
    }

    public function create()
    {
        return view('admin.obat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'kemasan' => ['nullable', 'string', 'max:35'],
            'harga' => ['required', 'integer', 'min:0'],
        ]);

        Obat::create($validated);

        return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan.');
    }

    public function edit(Obat $obat)
    {
        return view('admin.obat.edit', compact('obat'));
    }

    public function update(Request $request, Obat $obat)
    {
        $validated = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255'],
            'kemasan' => ['nullable', 'string', 'max:35'],
            'harga' => ['required', 'integer', 'min:0'],
        ]);

        $obat->update($validated);

        return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat)
    {
        try {
            $obat->delete();
        } catch (QueryException $exception) {
            Log::warning('Obat could not be deleted because it is referenced by related data.', [
                'obat_id' => $obat->id,
                'exception' => $exception,
            ]);

            return redirect()->route('obat.index')
                ->with('error', 'Obat tidak dapat dihapus karena sudah digunakan pada data pemeriksaan.');
        }

        return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus.');
    }
}
