@extends('emails.layout')

@section('title', 'Credenciais alteradas - Portal de Reclamações')

@section('protocolo')
    Segurança • Portal de Reclamações
@endsection

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:18px; font-weight:700; color:#3b1e7a;">
        Credenciais alteradas
    </h2>

    <p style="margin:0 0 24px 0; font-size:16px; line-height:1.6; color:#475569;">
        Olá, {{ $user->first_name }}. As credenciais de acesso à sua conta foram alteradas.
    </p>

    <div style="margin:30px 0; padding:20px; background:#f5f3ff; border-radius:12px; border:2px dashed #ddd6fe;">
        <p style="margin:0 0 12px 0; font-size:15px; line-height:1.5; color:#475569;">
            <strong>Email:</strong> {{ $user->email }}
        </p>
        <p style="margin:0; font-size:15px; line-height:1.5; color:#475569;">
            <strong>Nova senha:</strong>
            <strong style="color:#3b1e7a;">{{ $password }}</strong>
        </p>
    </div>

    <div style="background:#f8fafc; border-left:4px solid #e61575; padding:15px 20px; margin-bottom:30px; border-radius:0 8px 8px 0;">
        <p style="margin:0; font-size:14px; line-height:1.5; color:#475569;">
            Se não reconhece esta alteração, contacte o administrador do sistema.
        </p>
    </div>
@endsection
