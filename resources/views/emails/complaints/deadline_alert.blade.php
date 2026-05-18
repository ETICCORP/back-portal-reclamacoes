@extends('emails.layout')

@section('title', 'Alerta de Prazo Limite - Portal de Reclamações')

@section('protocolo', 'Urgente')

@section('content')
    <div style="text-align: center; margin-bottom: 30px;">
        <div
            style="display:inline-block; background:#e61575; color:#ffffff; padding:8px 24px; border-radius:50px; font-size:14px; font-weight:700; box-shadow:0 8px 15px rgba(230,21,117,0.25);">
            Restam {{ $deadline->remainingDays() }} dias
        </div>
    </div>

    <h2 style="margin:0 0 16px 0; font-size:20px; font-weight:700; color:#3b1e7a;">
        Olá,
    </h2>
    <p style="margin:0 0 20px 0; font-size:16px; line-height:1.6; color:#475569;">
        Verificámos uma pendência crítica no sistema que requer atenção imediata:
    </p>
    <p style="margin:0 0 30px 0; font-size:15px; line-height:1.7; color:#1e293b;">
        A reclamação identificada pelo protocolo <strong style="color:#e61575;">#{{ $deadline->complaint->code }}</strong> está prestes
        a atingir o seu limite legal para resposta.
    </p>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:25px; margin-bottom:35px;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="50%" style="padding-bottom:15px;">
                    <div
                        style="font-size:11px; color:#9d1c7f; font-weight:800; text-transform:uppercase; letter-spacing: 0.05em;">
                        Status Atual</div>
                    <div style="font-size:15px; font-weight:700; color:#3b1e7a; margin-top: 2px;">{{ $deadline->status }}
                    </div>
                </td>
                <td width="50%" style="padding-bottom:15px;">
                    <div
                        style="font-size:11px; color:#9d1c7f; font-weight:800; text-transform:uppercase; letter-spacing: 0.05em;">
                        Data de Início</div>
                    <div style="font-size:15px; color:#475569; margin-top: 2px; font-weight: 500;">
                        {{ $deadline->start_date?->format('d/m/Y') }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:15px; border-top:1px solid #e2e8f0;">
                    <div
                        style="font-size:11px; color:#9d1c7f; font-weight:800; text-transform:uppercase; letter-spacing: 0.05em;">
                        Data Limite (Deadline)</div>
                    <div style="font-size:18px; font-weight:800; color:#e61575; margin-top: 2px;">
                        {{ $deadline->end_date?->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <p style="margin-bottom:20px; font-size:14px; color:#64748b; font-weight: 500;">Aceda imediatamente para
                    evitar incumprimentos de SLA:</p>
                <a href="{{ url('/complaints/' . $deadline->complaint_id) }}"
                    style="display:inline-block; padding:16px 40px; background-color:#3b1e7a; color:#ffffff; font-weight:700; font-size:15px; text-decoration:none; border-radius:12px; box-shadow:0 10px 20px rgba(59,30,122,0.15); transition: background 0.2s;">
                    Resolver Pendência Agora
                </a>
            </td>
        </tr>
    </table>
@endsection
