# Poliklinik App v2 - Fast Track Bengkel Koding Web Developer

## Identitas

- **Nama:** Muchamad Nafis Aljufri
- **NIM:** A11.2023.15328
- **Kelas:** DEV-02
- **Mata Kuliah:** Bengkel Koding

## Deskripsi

Aplikasi manajemen poliklinik berbasis web (Laravel 12) dengan tiga role: **Admin**, **Dokter**, dan **Pasien**.
Repository ini memuat fitur dasar (pendaftaran poli, jadwal, pemeriksaan, resep obat) dan fitur lanjutan Fast Track.

## Fitur Lanjutan (Fast Track)

1. **Dashboard Pasien**: banner antrian aktif, tabel jadwal, aturan satu antrian aktif per pasien.
2. **Sistem Antrian Real-Time**: nomor yang sedang dilayani berubah tanpa reload (Laravel Reverb + Laravel Echo).
3. **Notifikasi**: ikon lonceng, daftar notifikasi, tandai dibaca (Notification class + database channel).
4. **Riwayat Pendaftaran Poli**: riwayat dan halaman detail pemeriksaan pasien.
5. **Upload Bukti Pembayaran**: pasien upload bukti, admin memverifikasi menjadi lunas.
6. **Export Excel**: Dokter, Pasien, Obat (Admin); Jadwal Periksa dan Riwayat Pasien (Dokter).
7. **Ganti Password**: menu Profile untuk semua role, validasi lewat Form Request.

## Teknologi

Laravel 12, PHP 8.2+, MySQL, Blade, Tailwind CSS + daisyUI, Vite,
Laravel Reverb, Laravel Echo, maatwebsite/excel.

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru (ekstensi: `gd`, `zip`, `mbstring`, `xml`, `fileinfo`)
- Composer
- Node.js 18+ dan npm
- MySQL / MariaDB

## Instalasi

```bash
git clone <URL-REPOSITORY>
cd poliklinik-app

composer install
npm install

copy .env.example .env        # Linux/Mac: cp .env.example .env
php artisan key:generate
```

Buat database kosong bernama `poliklinik`, lalu atur koneksi di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=poliklinik
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=reverb
```

Pastikan variabel `REVERB_*` dan `VITE_REVERB_*` terisi (lihat `.env.example`), lalu:

```bash
php artisan migrate --seed
php artisan storage:link
```

## Menjalankan Aplikasi

Buka **3 terminal** terpisah:

```bash
php artisan serve            # server aplikasi  -> http://127.0.0.1:8000
npm run dev                  # Vite (aset frontend)
php artisan reverb:start     # WebSocket server untuk antrian real-time
```

## Akun Demo

| Role | Email | Password |
|---|---|---|
| Admin | admin@gmail.com | admin |
| Dokter | dokter@gmail.com | dokter |
| Pasien | pasien@gmail.com | pasien |

## Alur Data Awal

1. Login **Admin**: buat Poli, atur poli untuk akun Dokter, tambah Obat (harga dan stok).
2. Login **Dokter**: buat Jadwal Periksa.
3. Login **Pasien**: daftar periksa, lalu ikuti antrian.
4. **Dokter** memeriksa pasien, **Pasien** upload bukti bayar, **Admin** memverifikasi.

## Struktur Penting

| Lokasi | Isi |
|---|---|
| `app/Http/Controllers/Pasien` | Dashboard, Pendaftaran Poli, Riwayat, Pembayaran |
| `app/Http/Controllers/Admin` | CRUD master, Verifikasi Pembayaran, Export |
| `app/Http/Controllers/Dokter` | Jadwal, Periksa, Riwayat, Export |
| `app/Events/AntrianUpdated.php` | Event siaran antrian real-time |
| `app/Notifications` | Notifikasi pembayaran (dokter dan pasien) |
| `app/Exports` | Kelas export Excel |
| `app/Http/Requests/UpdatePasswordRequest.php` | Validasi ganti password |
| `resources/js/app.js` | Listener Laravel Echo |

## Catatan

- File `.env` tidak disertakan di repository (berisi konfigurasi lokal).
- Antrian real-time membutuhkan `php artisan reverb:start` yang aktif.
- Jangan menjalankan `migrate:fresh` pada data uji kecuali memang ingin menghapus semuanya.