@extends('emails.layout')

@section('title', 'Processo Recusado - Portal de Reclamações')

@section('protocolo', 'Protocolo: #' . ($complaint->code ?? 'N/D'))

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:20px; font-weight:700; color:#3b1e7a;">
        Olá, {{ explode(' ', trim($complaint->full_name ?? 'Cliente'))[0] }}.
    </h2>
    
    <p style="margin:0 0 26px 0; font-size:16px; line-height:1.6; color:#475569;">
        Informamos que a sua reclamação foi analisada e não foi possível dar seguimento ao processo durante a fase de triagem.
    </p>

    <div style="margin-bottom:30px; text-align:center; padding:22px; background-color:#fdf2f8; border-radius:10px; border:1px dashed #fbcfe8;">
        <span style="font-size:12px; color:#9d1c7f; text-transform:uppercase; font-weight:700; display:block; margin-bottom:6px;">
            Estado Actual do Processo:
        </span>
        <span style="display:inline-block; padding:8px 24px; background-color:#e61575; color:#ffffff; font-weight:700; font-size:15px; border-radius:6px; text-transform:uppercase; letter-spacing: 0.02em;">
            {{ $complaint->status }}
        </span>
    </div>

    @if ($triage && $triage->is_refused)
        <div style="margin-bottom:32px; padding:24px; background-color:#f8fafc; border-radius:5px; border-left:4px solid #9d1c7f;">
            <h4 style="margin:0 0 10px 0; font-size:13px; font-weight:700; color:#3b1e7a; text-transform:uppercase; letter-spacing:0.025em;">
                Motivo da Recusa / Fundamentação:
            </h4>
            <div style="font-size:15px; line-height:1.6; color:#334155; font-style: italic;">
                "{{ $triage->refusal_reason }}"
            </div>
        </div>
    @endif

    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
        <p style="margin:0; font-size:15px; color:#64748b; text-align:center; line-height:1.6;">
            Caso necessite de esclarecimentos adicionais, poderá consultar os detalhes completos da análise diretamente na sua área de acompanhamento no portal.
        </p>
    </div>
@endsection