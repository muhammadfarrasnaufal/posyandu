import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'Posyandu Next.js',
  description: 'Next.js frontend untuk aplikasi Posyandu.',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="id">
      <body>{children}</body>
    </html>
  );
}
