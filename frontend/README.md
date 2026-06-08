# Posyandu Frontend

Next.js frontend untuk aplikasi Posyandu.

## Instalasi

1. Buka terminal di folder `frontend`
2. Jalankan `npm install`
3. Jalankan `npm run dev`

## Struktur

- `app/page.tsx` - halaman beranda
- `app/layout.tsx` - layout global
- `app/globals.css` - styling dasar

## Integrasi dengan backend

Frontend ini dapat dikoneksikan ke backend CodeIgniter melalui proxy API internal.

1. Salin `.env.example` menjadi `.env.local`
2. Setel `BACKEND_API_URL` ke URL backend Anda, misalnya `http://localhost/posyandu`
3. Jalankan `npm run dev` dan buka `http://localhost:3000/dashboard`

Proxy akan meneruskan permintaan ke backend CodeIgniter dan membantu menjaga session cookie dari browser.

- Halaman login: `/login`
- Halaman register: `/register`
- Halaman admin: `/admin`
- Halaman user: `/user`
- Halaman dashboard otomatis redirect: `/dashboard`
