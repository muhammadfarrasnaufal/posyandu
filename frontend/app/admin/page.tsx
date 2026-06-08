'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

type RecordItem = {
  id: number;
  nama: string;
  jenis_kelamin: string;
  owner_name: string | null;
  tanggal_kunjungan: string;
  berat_badan: string;
  tinggi_badan: string;
};

type NotificationItem = {
  id: number;
  title: string;
  body: string;
};

type ScheduleItem = {
  id: number;
  child_name: string;
  vaccine_name: string;
  jadwal: string;
  status: string;
};

type AdminDashboard = {
  user: {
    fullname: string;
    email: string;
  };
  stats: {
    total: number;
    today: number;
    recent: number;
    users: number;
    upcoming: number;
  };
  records: RecordItem[];
  notifications: NotificationItem[];
  schedules: ScheduleItem[];
};

export default function AdminPage() {
  const router = useRouter();
  const [data, setData] = useState<AdminDashboard | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    async function loadDashboard() {
      try {
        const sessionResponse = await fetch('/api/proxy/auth/session', {
          credentials: 'include',
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });
        const sessionData = await sessionResponse.json();

        if (!sessionData.logged_in) {
          router.replace('/login');
          return;
        }

        if (sessionData.role !== 'admin') {
          router.replace('/dashboard');
          return;
        }

        const response = await fetch('/api/proxy/admin/dashboard_json', {
          credentials: 'include',
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (!response.ok) {
          if (response.status === 403) {
            router.replace('/login');
            return;
          }
          throw new Error('Tidak dapat memuat data admin.');
        }

        const dashboardData = await response.json();
        setData(dashboardData);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Terjadi kesalahan.');
      } finally {
        setLoading(false);
      }
    }

    loadDashboard();
  }, [router]);

  async function handleLogout() {
    await fetch('/api/proxy/auth/logout', {
      method: 'GET',
      credentials: 'include',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      redirect: 'manual',
    });
    router.push('/login');
  }

  return (
    <main className="container">
      <section className="hero">
        <h1>Dashboard Admin</h1>
        <p>Kelola data Posyandu, notifikasi, dan jadwal imunisasi dari Next.js.</p>
      </section>

      <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '1rem', marginTop: '1.25rem' }}>
        <button className="button" style={{ background: '#ef4444' }} onClick={handleLogout}>
          Logout
        </button>
      </div>

      {loading ? (
        <div className="card">
          <h2>Memuat dashboard admin...</h2>
        </div>
      ) : error ? (
        <div className="card">
          <h2>Terjadi kesalahan</h2>
          <p>{error}</p>
        </div>
      ) : data ? (
        <>
          <div className="grid" style={{ marginTop: '1.5rem' }}>
            <div className="card">
              <h2>Total Rekam</h2>
              <p>{data.stats.total}</p>
            </div>
            <div className="card">
              <h2>Rekam Hari Ini</h2>
              <p>{data.stats.today}</p>
            </div>
            <div className="card">
              <h2>Jadwal 2 Minggu</h2>
              <p>{data.stats.upcoming}</p>
            </div>
            <div className="card">
              <h2>Pengguna</h2>
              <p>{data.stats.users}</p>
            </div>
          </div>

          <div className="grid" style={{ marginTop: '1.5rem' }}>
            <div className="card" style={{ flex: 2 }}>
              <h2>Notifikasi Terbaru</h2>
              {data.notifications.length === 0 ? (
                <p>Tidak ada notifikasi.</p>
              ) : (
                <ul>
                  {data.notifications.slice(0, 6).map((item) => (
                    <li key={item.id} style={{ marginBottom: '1rem' }}>
                      <strong>{item.title}</strong>
                      <p>{item.body}</p>
                    </li>
                  ))}
                </ul>
              )}
            </div>
            <div className="card" style={{ flex: 1 }}>
              <h2>Selamat datang</h2>
              <p>{data.user.fullname}</p>
              <p style={{ color: '#6b7280' }}>{data.user.email}</p>
            </div>
          </div>

          <div className="card" style={{ marginTop: '1.5rem' }}>
            <h2>Jadwal Imunisasi Mendatang</h2>
            {data.schedules.length === 0 ? (
              <p>Tidak ada jadwal imminisasi dalam dua minggu ke depan.</p>
            ) : (
              <table className="table table-striped" style={{ width: '100%' }}>
                <thead>
                  <tr>
                    <th>Balita</th>
                    <th>Vaksin</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  {data.schedules.map((schedule) => (
                    <tr key={schedule.id}>
                      <td>{schedule.child_name}</td>
                      <td>{schedule.vaccine_name}</td>
                      <td>{schedule.jadwal}</td>
                      <td>{schedule.status}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>

          <div className="card" style={{ marginTop: '1.5rem' }}>
            <h2>Rekam Posyandu Terbaru</h2>
            {data.records.length === 0 ? (
              <p>Tidak ada data rekam.</p>
            ) : (
              <ul>
                {data.records.slice(0, 6).map((record) => (
                  <li key={record.id} style={{ marginBottom: '0.75rem' }}>
                    <strong>{record.nama}</strong> • {record.jenis_kelamin} • {record.owner_name || 'Umum'} • {record.tanggal_kunjungan}
                  </li>
                ))}
              </ul>
            )}
          </div>
        </>
      ) : null}
    </main>
  );
}
