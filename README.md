# Sistem Tanda Tangan QR Code

Sistem digital untuk mengelola dokumen dengan tanda tangan berbasis QR Code menggunakan Laravel.

## Fitur

- ✅ Upload dokumen (PDF, DOC, DOCX, JPG, PNG)
- ✅ Generate QR Code otomatis untuk setiap dokumen
- ✅ Tanda tangan digital menggunakan canvas HTML5
- ✅ Verifikasi dokumen dengan scan QR Code
- ✅ Tracking status dokumen (draft, pending, signed, expired)
- ✅ Manajemen tanda tangan dengan informasi penandatangan
- ✅ Sistem kedaluwarsa dokumen (opsional)

## Persyaratan

- PHP >= 8.2
- Composer
- SQLite (default) atau MySQL/MariaDB
- Node.js & NPM (untuk assets)

## Instalasi

1. Clone atau download project ini

2. Install dependencies:
```bash
composer install
npm install
```

3. Setup environment (jika belum):
```bash
cp .env.example .env
php artisan key:generate
```

4. Pastikan database configuration di `.env`:
```
DB_CONNECTION=sqlite
```

Untuk MySQL, ubah menjadi:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tanda_tangan_qrcode
DB_USERNAME=root
DB_PASSWORD=
```

5. Jalankan migration:
```bash
php artisan migrate
```

6. Buat symbolic link untuk storage:
```bash
php artisan storage:link
```

7. Build assets (development):
```bash
npm run dev
```

Atau untuk production:
```bash
npm run build
```

## Penggunaan

### 1. Upload Dokumen

- Akses aplikasi di browser
- Klik "Upload Dokumen Baru"
- Isi informasi dokumen:
  - Judul dokumen (wajib)
  - Deskripsi (opsional)
  - File dokumen (PDF, DOC, DOCX, JPG, PNG - max 10MB)
  - Tanggal kedaluwarsa (opsional)
- Klik "Upload & Generate QR Code"

### 2. Mendapatkan QR Code

- Setelah upload, QR Code akan otomatis tergenerate
- QR Code dapat di-download atau dicetak dari halaman detail dokumen
- Link QR Code dapat dibagikan langsung untuk penandatanganan

### 3. Menandatangani Dokumen

- Scan QR Code dengan smartphone atau akses link langsung
- Isi informasi penandatangan:
  - Nama lengkap (wajib)
  - Email (opsional)
  - Jabatan (opsional)
- Buat tanda tangan menggunakan mouse/touchpad di canvas
- Klik "Tandatangani Dokumen"

### 4. Verifikasi Dokumen

- Akses link verifikasi atau scan QR Code
- Lihat detail dokumen dan semua tanda tangan yang sudah ada
- Informasi penandatangan akan ditampilkan beserta waktu penandatanganan

## Struktur Database

### Tabel `documents`
- id
- title
- description
- file_path
- original_filename
- file_type
- qr_code_token (unik)
- qr_code_path
- status (draft, pending, signed, expired)
- expires_at
- user_id
- timestamps

### Tabel `signatures`
- id
- document_id
- signer_name
- signer_email
- signer_position
- signature_data (base64 image)
- ip_address
- signed_at
- metadata
- timestamps

## Route

- `GET /` - Redirect ke daftar dokumen
- `GET /documents` - Daftar semua dokumen
- `GET /documents/create` - Form upload dokumen baru
- `POST /documents` - Simpan dokumen baru
- `GET /documents/{id}` - Detail dokumen
- `GET /documents/{id}/download` - Download dokumen
- `DELETE /documents/{id}` - Hapus dokumen
- `GET /sign/{token}` - Form tanda tangan (via QR Code)
- `POST /sign/{token}` - Proses tanda tangan
- `GET /sign/{token}/success` - Halaman sukses setelah tanda tangan
- `GET /verify/{token}` - Verifikasi dokumen

## Teknologi yang Digunakan

- **Laravel 11** - PHP Framework
- **SimpleSoftwareIO/simple-qrcode** - Library QR Code
- **Intervention Image** - Image processing
- **Tailwind CSS** - CSS Framework
- **HTML5 Canvas** - Signature pad

## Catatan

- File dokumen disimpan di `storage/app/public/documents`
- QR Code disimpan di `storage/app/public/qrcodes`
- Pastikan folder `storage/app/public` memiliki permission write
- Untuk production, pastikan `APP_ENV=production` dan `APP_DEBUG=false` di file `.env`

## Lisensi

MIT License
