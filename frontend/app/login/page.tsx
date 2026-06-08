'use client';

import Link from 'next/link';
import { FormEvent, useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

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
        const data = await response.json();
        if (data.logged_in) {
          router.replace('/dashboard');
        }
      } catch {
        // ignore session check failures on login page
      }
    }

    checkSession();
  }, [router]);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setError('');
    setLoading(true);

    try {
      const body = new URLSearchParams();
      body.append('email', email);
      body.append('password', password);

      const response = await fetch('/api/proxy/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: body.toString(),
        credentials: 'include',
        redirect: 'manual',
      });

      if (response.status >= 300 && response.status < 400) {
        const location = response.headers.get('location') || '/dashboard';
        const nextPath = new URL(location, window.location.href).pathname;
        router.push(nextPath);
        return;
      }

      const contentType = response.headers.get('content-type') || '';
      if (contentType.includes('application/json')) {
        const json = await response.json();
        if (json.success) {
          router.push(json.redirect || '/dashboard');
          return;
        }
        setError(json.message || 'Gagal login. Silakan coba lagi.');
        return;
      }

      const text = await response.text();
      if (text.includes('Email atau password salah') || response.status === 200) {
        setError('Email atau password salah.');
      } else {
        setError('Gagal login. Silakan coba lagi.');
      }
    } catch (err) {
      setError('Gagal menghubungi server. Periksa koneksi Anda.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="container">
      <section className="hero">
        <h1>Masuk ke Posyandu</h1>
        <p>Gunakan akun admin atau user untuk mengakses dashboard. Login akan diteruskan ke backend CodeIgniter.</p>
      </section>

      <div className="card" style={{ maxWidth: '520px', margin: '2rem auto' }}>
        <h2>Login</h2>
        <form onSubmit={handleSubmit}>
          <div className="mb-3">
            <label className="form-label">Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="form-control"
              required
            />
          </div>
          <div className="mb-3">
            <label className="form-label">Password</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="form-control"
              required
            />
          </div>
          {error ? <p style={{ color: '#b91c1c' }}>{error}</p> : null}
          <button type="submit" className="button" disabled={loading}>
            {loading ? 'Memproses...' : 'Masuk'}
          </button>
        </form>
        <p style={{ marginTop: '1.25rem' }}>
          Belum punya akun? <Link href="/register">Daftar di sini</Link>
        </p>
      </div>
    </main>
  );
}
