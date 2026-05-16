@extends('emails.layout')

@section('title', 'Atualização de Processo - Portal de Reclamações')

@section('protocolo', 'Protocolo: #' . ($complaint->code ?? 'N/D'))

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:20px; font-weight:700; color:#3b1e7a;">
        Olá, {{ explode(' ', trim($complaint->full_name ?? 'Cliente'))[0] }}.
    </h2>
    
    <p style="margin:0 0 26px 0; font-size:16px; line-height:1.6; color:#475569;">
        Informamos que o seu processo registou uma nova movimentação. O estado atual da sua exposição foi atualizado:
    </p>

    <div style="margin-bottom:30px; text-align:center; padding:22px; background-color:#f5f3ff; border-radius:10px; border:1px dashed #ddd6fe;">
        <span style="font-size:12px; color:#7c3aed; text-transform:uppercase; font-weight:700; display:block; margin-bottom:6px;">
            Novo Estado:
        </span>
        <span style="display:inline-block; padding:8px 24px; background-color:#3b1e7a; color:#ffffff; font-weight:700; font-size:15px; border-radius:6px; text-transform:uppercase; letter-spacing: 0.02em;">
            {{ $complaint->status }}
        </span>
    </div>

    <div style="margin-bottom:32px;">
        <h4 style="margin:0 0 10px 0; font-size:13px; font-weight:700; color:#3b1e7a; text-transform:uppercase; letter-spacing:0.025em;">
            O que isto significa:
        </h4>
        <div style="font-size:15px; line-height:1.6; color:#475569; border-left:4px solid #3b1e7a; padding-left:16px;">
            {{ $statusMessage }}
        </div>
    </div>

    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
        <p style="margin:0; font-size:14px; color:#64748b; text-align:center; line-height:1.6;">
            Pode consultar o histórico completo a qualquer momento através do portal de acompanhamento.
        </p>
    </div>
@endsection