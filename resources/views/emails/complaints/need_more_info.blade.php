@extends('emails.layout')

@section('title', 'Ação Necessária (Devolução) - Portal de Reclamações')

@section('protocolo', 'Protocolo: #' . ($complaint->code ?? 'N/D'))

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:20px; font-weight:700; color:#3b1e7a;">
        Olá, {{ explode(' ', trim($complaint->full_name ?? 'Cliente'))[0] }}.
    </h2>
    
    <p style="margin:0 0 26px 0; font-size:16px; line-height:1.6; color:#475569;">
        Informamos que a sua reclamação registou uma nova atualização durante a fase de triagem e necessita de retificações.
    </p>

    <div style="margin-bottom:30px; text-align:center; padding:22px; background-color:#f5f3ff; border-radius:10px; border:1px dashed #ddd6fe;">
        <span style="font-size:12px; color:#7c3aed; text-transform:uppercase; font-weight:700; display:block; margin-bottom:6px;">
            Estado Atual:
        </span>
        <span style="display:inline-block; padding:8px 24px; background-color:#9d1c7f; color:#ffffff; font-weight:700; font-size:15px; border-radius:6px; text-transform:uppercase; letter-spacing: 0.02em;">
            {{ $complaint->status }}
        </span>
    </div>

    @if ($triage && $triage->is_returned)
        <div style="margin-bottom:32px; padding:24px; background-color:#f8fafc; border-radius:10px; border-left:4px solid #9d1c7f;">
            <h4 style="margin:0 0 10px 0; font-size:13px; font-weight:700; color:#3b1e7a; text-transform:uppercase; letter-spacing:0.025em;">
                Motivo da Devolução:
            </h4>
            <div style="font-size:15px; line-height:1.6; color:#334155; font-style: italic;">
                "{{ $triage->return_reason }}"
            </div>
        </div>
    @endif

    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
        <p style="margin:0; font-size:15px; color:#64748b; text-align:center; line-height:1.6;">
            Por favor, aceda ao portal para retificar as informações solicitadas. O histórico completo está disponível na sua área de acompanhamento.
        </p>
    </div>
@endsection