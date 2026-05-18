@extends('emails.layout')

@section('title', "Parecer Técnico - Reclamação #{$complaint->code}")

@section('protocolo', "ID: #{$complaint->code}")

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:20px; font-weight:700; color:#3b1e7a;">
        Notificação de Actualização
    </h2>
    
    <p style="margin:0 0 24px 0; font-size:16px; line-height:1.6; color:#475569;">
        Informamos que foi submetido um novo parecer técnico relativo ao processo de reclamação identificado abaixo.
    </p>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:30px; border-collapse:collapse;">
        <tr>
            <td style="padding:18px; background-color:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
                            <span style="font-size:11px; color:#9d1c7f; text-transform:uppercase; font-weight:700; letter-spacing:0.05em;">
                                Detalhes do Processo
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:12px;">
                            <p style="margin:4px 0; font-size:14px;">
                                <strong style="color:#1e293b;">Protocolo:</strong> 
                                <span style="color:#e61575; font-weight:700;">#{{ $complaint->code }}</span>
                            </p>
                            <p style="margin:4px 0; font-size:14px;">
                                <strong style="color:#1e293b;">Titular:</strong> 
                                <span style="color:#475569; font-weight:500;">{{ $complaint->full_name }}</span>
                            </p>
                            <p style="margin:4px 0; font-size:14px;">
                                <strong style="color:#1e293b;">Técnico Responsável:</strong> 
                                <span style="color:#475569; font-weight:500;">{{ $opinion?->user?->first_name . ' ' . $opinion?->user?->last_name ?? 'N/A' }}</span>
                            </p>
                            <p style="margin:4px 0; font-size:14px;">
                                <strong style="color:#1e293b;">Data do Parecer:</strong> 
                                <span style="color:#475569; font-weight:500;">{{ Carbon\Carbon::parse($opinion->submitted_at)->format('d/m/Y H:i') }}</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-bottom:20px;">
        <h4 style="margin:0 0 12px 0; font-size:13px; font-weight:700; color:#3b1e7a; text-transform:uppercase; letter-spacing:0.03em;">
            Parecer Técnico / Decisão:
        </h4>
        <div style="font-size:15px; line-height:1.6; color:#1e293b; background-color:#fffbeb; border-left:4px solid #f59e0b; padding:18px; border-radius:0 12px 12px 0;">
            {!! html_entity_decode($opinion->opinion ?? 'Sem conteúdo descritivo.') !!}
        </div>
    </div>
@endsection