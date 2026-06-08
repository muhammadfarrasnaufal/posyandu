'use client';

import { ChangeEvent, FormEvent, useEffect, useState } from 'react';
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
  const [avatarUrl, setAvatarUrl] = useState<string | null>(null);
  const [profileFullname, setProfileFullname] = useState('');
  const [profileEmail, setProfileEmail] = useState('');
  const [selectedAvatar, setSelectedAvatar] = useState<File | null>(null);
  const [profileMessage, setProfileMessage] = useState('');
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

        setAvatarUrl(sessionData.avatar_url || null);

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
        setProfileFullname(dashboardData.user.fullname);
        setProfileEmail(dashboardData.user.email);
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

  function handleAvatarSelection(event: ChangeEvent<HTMLInputElement>) {
    setSelectedAvatar(event.target.files?.[0] || null);
  }

  async function handleProfileSave(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setProfileMessage('');

    try {
      const formData = new FormData();
      formData.append('fullname', profileFullname);
      formData.append('email', profileEmail);
      if (selectedAvatar) {
        formData.append('avatar', selectedAvatar);
      }

      const response = await fetch('/api/proxy/auth/update_profile', {
        method: 'POST',
        body: formData,
        credentials: 'include',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
        },
      });

      const json = await response.json();
      if (!response.ok || !json.success) {
        setProfileMessage(json.message || 'Gagal memperbarui profil.');
        return;
      }

      setAvatarUrl(json.profile.avatar_url || null);
      setProfileFullname(json.profile.fullname);
      setProfileEmail(json.profile.email);
      setSelectedAvatar(null);
      setProfileMessage('Profil berhasil diperbarui.');
    } catch (err) {
      setProfileMessage('Terjadi kesalahan saat memperbarui profil.');
    }
  }

  async function handleRemoveAvatar() {
    setProfileMessage('');

    try {
      const formData = new FormData();
      formData.append('fullname', profileFullname);
      formData.append('email', profileEmail);
      formData.append('remove_avatar', '1');

      const response = await fetch('/api/proxy/auth/update_profile', {
        method: 'POST',
        body: formData,
        credentials: 'include',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
        },
      });

      const json = await response.json();
      if (!response.ok || !json.success) {
        setProfileMessage(json.message || 'Gagal menghapus foto profil.');
        return;
      }

      setAvatarUrl(null);
      setSelectedAvatar(null);
      setProfileMessage('Foto profil berhasil dihapus.');
    } catch (err) {
      setProfileMessage('Terjadi kesalahan saat menghapus foto profil.');
    }
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

      <div className="card" style={{ marginTop: '1.5rem' }}>
        <h2>Profil Saya</h2>
        <form onSubmit={handleProfileSave}>
          <div className="mb-3" style={{ display: 'grid', gap: '1rem' }}>
            <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
              <img
                src={avatarUrl || 'https://via.placeholder.com/96?text=User'}
                alt="Avatar Pengguna"
                width={96}
                height={96}
                style={{ borderRadius: '9999px', objectFit: 'cover', background: '#f3f4f6' }}
              />
              <div>
                <p style={{ margin: 0, fontWeight: 600 }}>{data?.user.fullname || 'Pengguna'}</p>
                <p style={{ margin: 0, color: '#6b7280' }}>{data?.user.email}</p>
              </div>
            </div>

            <div>
              <label className="form-label">Nama Lengkap</label>
              <input
                type="text"
                value={profileFullname}
                onChange={(e) => setProfileFullname(e.target.value)}
                className="form-control"
                required
              />
            </div>

            <div>
              <label className="form-label">Email</label>
              <input
                type="email"
                value={profileEmail}
                onChange={(e) => setProfileEmail(e.target.value)}
                className="form-control"
                required
              />
            </div>

            <div>
              <label className="form-label">Foto Profil Baru</label>
              <input
                type="file"
                accept="image/*"
                onChange={handleAvatarSelection}
                className="form-control"
              />
            </div>

            {profileMessage ? <p style={{ color: '#047857' }}>{profileMessage}</p> : null}

            <div style={{ display: 'flex', gap: '0.75rem', flexWrap: 'wrap' }}>
              <button type="submit" className="button">
                Simpan Profil
              </button>
              <button type="button" className="button" style={{ background: '#ef4444' }} onClick={handleRemoveAvatar}>
                Hapus Foto Profil
              </button>
            </div>
          </div>
        </form>
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
