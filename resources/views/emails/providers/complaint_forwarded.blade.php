@extends('emails.layout')

@section('title', 'Nova Reclamação Encaminhada - Portal de Reclamações')

@section('protocolo', 'ID: #' . ($complaintProvider->complaint->code ?? 'N/D'))

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:18px; font-weight:700; color:#3b1e7a;">
        Prezado(a) {{ $complaintProvider->provider->name }},
    </h2>
    
    <p style="margin:0 0 26px 0; font-size:16px; line-height:1.6; color:#475569;">
        Informamos que uma nova exposição foi encaminhada para a sua análise e acompanhamento obrigatório dentro do prazo estabelecido.
    </p>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:25px; margin-bottom:35px;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td style="padding-bottom:15px;">
                    <div style="font-size:11px; color:#9d1c7f; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
                        Resumo do Encaminhamento
                    </div>
                    
                    <div style="margin-bottom:12px; font-size:15px; line-height:1.5;">
                        <span style="color:#64748b;">Assunto:</span> <strong style="color:#1e293b;">{{ $complaintProvider->summary }}</strong>
                    </div>

                    @if($complaintProvider->deadline)
                        <div style="display:inline-block; background:#fff2f8; border:1px solid #fbcfe8; color:#e61575; padding:6px 14px; border-radius:6px; font-size:13px; font-weight:700;">
                            ⏱ Prazo limite: {{ $complaintProvider->deadline->format('d/m/Y') }}
                        </div>
                    @endif
                </td>
            </tr>
            
            @if($complaintProvider->notes)
                <tr>
                    <td style="padding-top:15px; border-top:1px solid #e2e8f0;">
                        <div style="font-size:12px; font-weight:700; color:#3b1e7a; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.02em;">
                            Notas Internas:
                        </div>
                        <div style="font-size:14px; line-height:1.6; color:#475569; font-style: italic;">
                            {!! html_entity_decode($complaintProvider->notes ?? 'Sem descrição fornecida.') !!}
                        </div>
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <p style="margin-bottom:20px; font-size:14px; color:#64748b; font-weight: 500;">
                    Para consultar o processo completo e submeter a sua resposta:
                </p>
                <a href="{{ config('app.url') }}" 
                   style="display:inline-block; padding:16px 40px; background-color:#3b1e7a; color:#ffffff; font-weight:700; font-size:15px; text-decoration:none; border-radius:12px; box-shadow:0 10px 20px rgba(59,30,122,0.15); transition: background 0.2s;">
                    Aceder ao Painel do Prestador
                </a>
            </td>
        </tr>
    </table>
@endsection