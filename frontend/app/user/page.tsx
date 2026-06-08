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

type ScheduleItem = {
  id: number;
  child_name: string;
  vaccine_name: string;
  jadwal: string;
  status: string;
};

type UserDashboard = {
  user: {
    fullname: string;
    email: string;
  };
  stats: {
    total: number;
    upcoming_count: number;
    next_date: string | null;
    last_updated: string | null;
  };
  records: RecordItem[];
  upcoming_schedules: ScheduleItem[];
  next_schedule: ScheduleItem | null;
};

export default function UserPage() {
  const router = useRouter();
  const [data, setData] = useState<UserDashboard | null>(null);
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

        if (sessionData.role !== 'user') {
          router.replace('/dashboard');
          return;
        }

        const response = await fetch('/api/proxy/user/dashboard_json', {
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
          throw new Error('Tidak dapat memuat data pengguna.');
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
        <h1>Dashboard Pengguna</h1>
        <p>Pantau rekam posyandu dan jadwal imunisasi Anda secara langsung dari frontend Next.js.</p>
      </section>

      <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '1rem', marginTop: '1.25rem' }}>
        <button className="button" style={{ background: '#ef4444' }} onClick={handleLogout}>
          Logout
        </button>
      </div>

      {loading ? (
        <div className="card">
          <h2>Memuat dashboard pengguna...</h2>
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
              <h2>Jadwal Mendatang</h2>
              <p>{data.stats.upcoming_count}</p>
            </div>
            <div className="card">
              <h2>Jadwal Selanjutnya</h2>
              <p>{data.stats.next_date || 'Belum ada'}</p>
            </div>
            <div className="card">
              <h2>Nama</h2>
              <p>{data.user.fullname}</p>
            </div>
          </div>

          <div className="card" style={{ marginTop: '1.5rem' }}>
            <h2>Jadwal Imunisasi</h2>
            {data.upcoming_schedules.length === 0 ? (
              <p>Tidak ada jadwal imunisasi.</p>
            ) : (
              <ul>
                {data.upcoming_schedules.map((schedule) => (
                  <li key={schedule.id} style={{ marginBottom: '1rem' }}>
                    <strong>{schedule.child_name}</strong> • {schedule.vaccine_name} • {schedule.jadwal} • {schedule.status}
                  </li>
                ))}
              </ul>
            )}
          </div>

          <div className="card" style={{ marginTop: '1.5rem' }}>
            <h2>Rekam Posyandu</h2>
            {data.records.length === 0 ? (
              <p>Belum ada data rekam.</p>
            ) : (
              <ul>
                {data.records.map((record) => (
                  <li key={record.id} style={{ marginBottom: '0.75rem' }}>
                    <strong>{record.nama}</strong> • {record.jenis_kelamin} • {record.tanggal_kunjungan} • {record.berat_badan} kg
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
