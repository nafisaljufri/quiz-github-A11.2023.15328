<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JadwalPeriksaController extends Controller
{
    private const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $jadwals = JadwalPeriksa::where('id_dokter', Auth::user()->id)
            ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 WHEN 'Minggu' THEN 7 END")
            ->orderBy('jam_mulai')
            ->paginate(10);

        return view('dokter.jadwal-periksa.index', compact('jadwals'));
    }

    public function create()
    {
        return view('dokter.jadwal-periksa.create', ['hariList' => self::HARI]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());
        $validated['id_dokter'] = Auth::user()->id;

        JadwalPeriksa::create($validated);

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $jadwalPeriksa = $this->ownedSchedule($id);

        return view('dokter.jadwal-periksa.edit', [
            'jadwalPeriksa' => $jadwalPeriksa,
            'hariList' => self::HARI,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $jadwalPeriksa = $this->ownedSchedule($id);
        $validated = $request->validate($this->rules(), $this->messages());
        $jadwalPeriksa->update($validated);

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $jadwalPeriksa = $this->ownedSchedule($id);

        // The FK cascades to daftar_poli, so preserve registrations by refusing deletion.
        if ($jadwalPeriksa->daftarPolis()->exists()) {
            return redirect()->route('jadwal-periksa.index')
                ->with('error', 'Jadwal tidak dapat dihapus karena sudah digunakan pada pendaftaran pasien.');
        }

        try {
            $jadwalPeriksa->delete();
        } catch (QueryException $exception) {
            Log::warning('Jadwal periksa could not be deleted.', [
                'jadwal_id' => $jadwalPeriksa->id,
                'exception' => $exception,
            ]);

            return redirect()->route('jadwal-periksa.index')
                ->with('error', 'Jadwal periksa tidak dapat dihapus saat ini. Silakan coba kembali.');
        }

        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil dihapus.');
    }

    private function ownedSchedule(string $id): JadwalPeriksa
    {
        return JadwalPeriksa::where('id_dokter', Auth::user()->id)->findOrFail($id);
    }

    private function rules(): array
    {
        return [
            'hari' => ['required', 'string', Rule::in(self::HARI)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ];
    }

    private function messages(): array
    {
        return [
            'hari.required' => 'Hari wajib dipilih.',
            'hari.in' => 'Hari yang dipilih tidak valid.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Jam mulai harus menggunakan format waktu yang valid.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Jam selesai harus menggunakan format waktu yang valid.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar daripada jam mulai.',
        ];
    }
}
