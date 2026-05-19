@extends('emails.layout')

@section('title', 'Atualização do Processo - Reclamação Encaminhada')

@section('protocolo', 'ID: #' . ($latestForward->complaint->code ?? 'N/D'))

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:18px; font-weight:700; color:#3b1e7a;">
        Olá, {{ $latestForward->complaint->full_name }},
    </h2>

    <p style="margin:0 0 26px 0; font-size:16px; line-height:1.6; color:#475569;">
        Informamos que a sua reclamação avançou na nossa análise interna e foi encaminhada para o respetivo Provedor técnico
        para averiguação detalhada.
    </p>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:25px; margin-bottom:35px;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="padding-bottom:15px;">
                    <div
                        style="font-size:11px; color:#9d1c7f; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
                        Detalhes do Encaminhamento
                    </div>

                    <div style="margin-bottom:12px; font-size:15px; line-height:1.5;">
                        <span style="color:#64748b;">Âmbito da Análise:</span> <strong
                            style="color:#1e293b;">{{ $latestForward->summary }}</strong>
                    </div>

                    <div
                        style="display:inline-block; background:#f0fdf4; border:1px solid #bbf7d0; color:#16a34a; padding:6px 14px; border-radius:6px; font-size:13px; font-weight:700;">
                        🔄 Estado actual: Encaminhado para o Provedor
                    </div>
                </td>
            </tr>

            @if ($latestForward->notes)
                <tr>
                    <td style="padding-top:15px; border-top:1px solid #e2e8f0;">
                        <div
                            style="font-size:12px; font-weight:700; color:#3b1e7a; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.02em;">
                            Motivo do Encaminhamento:
                        </div>
                        <div style="font-size:14px; line-height:1.6; color:#475569; font-style: italic;">
                            {!! html_entity_decode($latestForward->notes ?? 'Análise técnica detalhada dos factos expostos.') !!}
                        </div>
                    </td>
                </tr>
            @endif
        </table>
    </div>

    </table>
@endsection
