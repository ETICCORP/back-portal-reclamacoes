@extends('emails.layout')

@section('title', 'Código de Autenticação - KEEPCOMPLY')

{{-- Define dinamicamente o texto superior do protocolo com base na marca activa --}}
@section('protocolo')
    Segurança • {{ config('app.branding', 'KEEPCOMPLY') }}
@endsection

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:18px; font-weight:700; color:#3b1e7a;">
        Olá, {{ $user->first_name }},
    </h2>

    <p style="margin:0 0 24px 0; font-size:16px; line-height:1.6; color:#475569;">
        Foi solicitada uma autenticação para a sua conta. O seu código de verificação de dois fatores (2FA) gerado pelo
        sistema é:
    </p>

    <div
        style="margin:30px 0; text-align:center; padding:24px; background-color:#f5f3ff; border-radius:12px; border:2px dashed #ddd6fe;">
        <h1
            style="margin:0; font-family: monospace; font-size:38px; font-weight:700; color:#3b1e7a; letter-spacing:6px; line-height: 1.2;">
            {{ $user->two_factor_code }}
        </h1>
    </div>

    <div
        style="background:#f8fafc; border-left:4px solid #e61575; padding:15px 20px; margin-bottom:30px; border-radius: 0 8px 8px 0;">
        <p style="margin:0; font-size:14px; line-height:1.5; color:#475569;">
            ⏱ Este código é estritamente confidencial e expira em <strong style="color:#e61575;">10 minutos</strong>.
        </p>
    </div>

    <p style="margin:0 0 10px 0; font-size:14px; color:#94a3b8; text-align:center; font-style: italic;">
        Se não solicitou este código, por razões de segurança, ignore esta mensagem ou contacte o administrador.
    </p>
@endsection
