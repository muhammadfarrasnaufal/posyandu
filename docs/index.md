# Dokumentasi Posyandu

Selamat datang di dokumentasi Posyandu. Folder `docs/` berisi panduan singkat untuk pengembangan dan pengaturan proyek.

## Ringkasan Proyek

Aplikasi ini menggabungkan:

- Backend PHP dengan CodeIgniter 3
- Frontend React/Next.js di folder `frontend/`
- Proxy API `app/api/proxy/[...backendPath]/route.ts` untuk meneruskan request dari frontend ke backend

## Menjalankan Proyek

1. Jalankan backend di XAMPP atau web server lokal.
2. Pastikan database dan konfigurasi CodeIgniter sudah benar.
3. Jalankan frontend dengan:
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

## Struktur Dokumentasi

- `README.md` — ringkasan proyek
- `docs/index.md` — halaman utama dokumentasi
- `.github/workflows/deploy-docs.yml` — workflow GitHub Pages

## Deploy GitHub Pages

Dokumentasi akan dipublish ke branch `gh-pages` secara otomatis setelah push ke branch `main`.

Jika GitHub Pages belum aktif, aktifkan di Settings > Pages dan pilih branch `gh-pages` sebagai sumber.
