# Posyandu

A CodeIgniter 3 backend with a modern Next.js frontend for a local Posyandu application.

## Struktur Repo

- `application/` — CodeIgniter PHP backend
- `system/` — CodeIgniter framework files
- `frontend/` — Next.js 15 frontend app
- `docs/` — documentation content for GitHub Pages

## Fitur Utama

- Autentikasi pengguna dan admin dengan session CodeIgniter
- API proxy Next.js untuk menghubungkan frontend dengan backend PHP
- Dashboard admin dan user berbasis JSON
- Dokumentasi repository yang siap dipublish ke GitHub Pages

## Pengaturan Lokal

1. Pastikan XAMPP atau server PHP berjalan.
2. Letakkan folder `posyandu` dalam folder web server Anda.
3. Konfigurasikan database di `application/config/database.php`.
4. Jalankan `frontend`:
   ```bash
   cd frontend
   npm install
   npm run dev
   ```
5. Akses backend melalui `http://localhost/posyandu` dan frontend melalui `http://localhost:3000`.

## Branch Git

- `main` adalah branch default sekarang.
- `master` masih ada di remote untuk kompatibilitas, tetapi `main` adalah branch utama untuk pengembangan.

## GitHub Pages

Dokumentasi di `docs/` akan dideploy otomatis melalui GitHub Actions ke branch `gh-pages` saat `main` di-push.

## Cara Kontribusi

1. Buat branch baru dari `main`
2. Tambahkan fitur atau perbaikan
3. Commit perubahan
4. Push branch dan buat pull request ke `main`
