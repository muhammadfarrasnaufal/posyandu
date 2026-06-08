import Link from 'next/link';

export default function Home() {
  return (
    <main className="container">
      <section className="hero">
        <h1>Posyandu Modern dengan Next.js</h1>
        <p>
          Ini adalah starter Next.js terbaru untuk proyek Posyandu Anda. Integrasikan frontend React dengan backend CodeIgniter menggunakan API JSON.
        </p>
        <div style={{ display: 'flex', gap: '1rem', flexWrap: 'wrap', marginTop: '1.5rem' }}>
          <Link href="/login" className="button">
            Masuk
          </Link>
          <Link href="/register" className="button" style={{ background: '#22c55e' }}>
            Daftar
          </Link>
        </div>
      </section>

      <section style={{ marginTop: '3rem' }}>
        <h2>UI Responsif</h2>
        <p>Desain modern dengan Next.js App Router dan CSS global untuk tampilan yang konsisten.</p>
      </section>

      <section style={{ marginTop: '2rem' }}>
        <h2>Routing Cepat</h2>
        <p>Halaman dikelola secara server-side dan client-side untuk performa lebih baik.</p>
      </section>

      <section style={{ marginTop: '2rem' }}>
        <h2>Integrasi API</h2>
        <p>Gunakan endpoint CodeIgniter eksisting untuk menampilkan data posyandu secara realtime.</p>
      </section>

      <section className="grid" style={{ marginTop: '2rem' }}>
        <div className="card">
          <h2>Dashboard Modern</h2>
          <p>Buat halaman dashboard Next.js untuk admin dan pengguna. Tambahkan API call ke backend agar data rekam dan jadwal imunisasi tampil langsung.</p>
        </div>
        <div className="card">
          <h2>Pengembangan Cepat</h2>
          <p>Jalankan <code>npm run dev</code> di folder <code>frontend</code> dan kembangkan frontend terpisah dari backend PHP.</p>
        </div>
      </section>

      <footer className="footer" style={{ marginTop: '3rem' }}>
        <p>Next.js 15 + React 19 • Starter frontend untuk Posyandu</p>
      </footer>
    </main>
  );
}
