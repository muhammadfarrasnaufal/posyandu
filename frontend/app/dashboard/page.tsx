'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

export default function DashboardRedirectPage() {
  const router = useRouter();
  const [status, setStatus] = useState('loading');

  useEffect(() => {
    async function checkSession() {
      try {
        const response = await fetch('/api/proxy/auth/session', {
          credentials: 'include',
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (!response.ok) {
          router.replace('/login');
          return;
        }

        const data = await response.json();
        if (!data.logged_in) {
          router.replace('/login');
          return;
        }

        if (data.role === 'admin') {
          router.replace('/admin');
        } else {
          router.replace('/user');
        }
      } catch (err) {
        setStatus('error');
      }
    }

    checkSession();
  }, [router]);

  return (
    <main className="container">
      <section className="hero">
        <h1>{status === 'loading' ? 'Memeriksa sesi...' : 'Terjadi kesalahan'}</h1>
        <p>
          {status === 'loading'
            ? 'Sedang memeriksa apakah Anda sudah login. Jika belum, Anda akan diarahkan ke halaman login.'
            : 'Tidak dapat memeriksa sesi. Silakan mulai ulang halaman atau masuk kembali.'}
        </p>
      </section>
    </main>
  );
}
