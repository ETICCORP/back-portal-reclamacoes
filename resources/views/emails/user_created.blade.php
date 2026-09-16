@extends('emails.layout')

@section('title', 'Credenciais de acesso - Portal de Reclamações')

@section('protocolo')
    Acesso • {{ config('app.branding', 'KEEPCOMPLY') }}
@endsection

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:18px; font-weight:700; color:#3b1e7a;">
        Bem-vindo ao Portal de Reclamações, {{ $user->first_name }}
    </h2>

    <p style="margin:0 0 24px 0; font-size:16px; line-height:1.6; color:#475569;">
        A sua conta no Portal de Reclamações foi criada com sucesso. Use os dados abaixo para fazer login:
    </p>

    <div style="margin:30px 0; padding:20px; background:#f5f3ff; border-radius:12px; border:2px dashed #ddd6fe;">
        <p style="margin:0 0 12px 0; font-size:15px; line-height:1.5; color:#475569;">
            <strong>Email:</strong> {{ $user->email }}
        </p>
        <p style="margin:0; font-size:15px; line-height:1.5; color:#475569;">
            <strong>Senha temporária:</strong>
            <strong style="color:#3b1e7a;">{{ $password }}</strong>
        </p>
    </div>

    <div style="background:#f8fafc; border-left:4px solid #e61575; padding:15px 20px; margin-bottom:30px; border-radius:0 8px 8px 0;">
        <p style="margin:0; font-size:14px; line-height:1.5; color:#475569;">
            Por segurança, altere a sua senha após o primeiro acesso.
        </p>
    </div>

    <p style="margin:0 0 10px 0; font-size:14px; color:#94a3b8; text-align:center; font-style:italic;">
        Caso não tenha solicitado esta conta, ignore esta mensagem ou contacte o administrador.
    </p>
@endsection
