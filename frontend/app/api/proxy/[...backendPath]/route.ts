import { NextRequest, NextResponse } from 'next/server';

async function proxyRequest(request: NextRequest, backendPath: string[]) {
  const backendBase = process.env.BACKEND_API_URL;
  if (!backendBase) {
    throw new Error('BACKEND_API_URL environment variable is not defined.');
  }
  const url = new URL(backendBase);
  url.pathname = [url.pathname.replace(/\/$/, ''), ...backendPath].join('/');
  url.search = request.nextUrl.search;

  const headers = new Headers(request.headers);
  headers.set('host', url.host);

  const body = ['GET', 'HEAD'].includes(request.method) ? undefined : await request.text();
  const response = await fetch(url.toString(), {
    method: request.method,
    headers,
    body,
    redirect: 'manual',
  });

  const resHeaders = new Headers(response.headers);
  resHeaders.delete('content-encoding');
  resHeaders.delete('transfer-encoding');

  return new NextResponse(response.body, {
    status: response.status,
    statusText: response.statusText,
    headers: resHeaders,
  });
}

export async function GET(request: NextRequest, context: any) {
  const params = context.params as { backendPath: string[] };
  return proxyRequest(request, params.backendPath);
}

export async function POST(request: NextRequest, context: any) {
  const params = context.params as { backendPath: string[] };
  return proxyRequest(request, params.backendPath);
}

export async function PUT(request: NextRequest, context: any) {
  const params = context.params as { backendPath: string[] };
  return proxyRequest(request, params.backendPath);
}

export async function DELETE(request: NextRequest, context: any) {
  const params = context.params as { backendPath: string[] };
  return proxyRequest(request, params.backendPath);
}
