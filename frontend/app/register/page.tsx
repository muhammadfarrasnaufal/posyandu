'use client';

import Link from 'next/link';
import { FormEvent, useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

export default function RegisterPage() {
  const router = useRouter();
  const [fullname, setFullname] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [role, setRole] = useState('user');
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
        // ignore session error on registration page
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
      body.append('fullname', fullname);
      body.append('email', email);
      body.append('password', password);
      body.append('confirm_password', confirmPassword);
      body.append('role', role);

      if (password !== confirmPassword) {
        setError('Password dan konfirmasi password tidak cocok.');
        setLoading(false);
        return;
      }

      const response = await fetch('/api/proxy/auth/register', {
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
        router.push('/login');
        return;
      }

      const contentType = response.headers.get('content-type') || '';
      if (contentType.includes('application/json')) {
        const json = await response.json();
        if (json.success) {
          router.push('/login');
          return;
        }
        setError(json.message || 'Gagal mendaftar. Periksa input dan coba lagi.');
        return;
      }

      const text = await response.text();
      if (text.includes('Pendaftaran berhasil')) {
        router.push('/login');
        return;
      }

      setError('Gagal mendaftar. Periksa input dan coba lagi.');
    } catch (err) {
      setError('Terjadi kesalahan jaringan.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <main className="container">
      <section className="hero">
        <h1>Daftar Akun Posyandu</h1>
        <p>Gunakan formulir ini untuk membuat akun admin atau user dan mengakses dashboard Next.js.</p>
      </section>

      <div className="card" style={{ maxWidth: '560px', margin: '2rem auto' }}>
        <h2>Register</h2>
        <form onSubmit={handleSubmit}>
          <div className="mb-3">
            <label className="form-label">Nama Lengkap</label>
            <input
              type="text"
              value={fullname}
              onChange={(e) => setFullname(e.target.value)}
              className="form-control"
              required
            />
          </div>

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

          <div className="mb-3">
            <label className="form-label">Konfirmasi Password</label>
            <input
              type="password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              className="form-control"
              required
            />
          </div>

          <div className="mb-3">
            <label className="form-label">Peran</label>
            <select value={role} onChange={(e) => setRole(e.target.value)} className="form-control">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          {error ? <p style={{ color: '#b91c1c' }}>{error}</p> : null}

          <button type="submit" className="button" disabled={loading}>
            {loading ? 'Memproses...' : 'Daftar'}
          </button>
        </form>

        <p style={{ marginTop: '1.25rem' }}>
          Sudah punya akun? <Link href="/login">Masuk di sini</Link>
        </p>
      </div>
    </main>
  );
}
