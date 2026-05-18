@extends('emails.layout')

@section('title', 'Confirmação de Receção de Reclamação')

@section('protocolo')
    Protocolo #{{ $complaint->code ?? 'N/D' }}
@endsection

@section('content')
    <h2 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 700; color: #0f172a;">
        Olá, {{ explode(' ', trim($complaint->full_name ?? 'Cliente'))[0] }}.
    </h2>

    <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #475569;">
        Confirmamos a receção da sua exposição. A nossa equipa de apoio ao cliente já foi notificada e iniciará a análise
        técnica do processo imediatamente.
    </p>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="margin-bottom: 28px; border-collapse: collapse;">
        <tr>
            <td style="padding: 18px; background-color: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                            <span
                                style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                                Detalhes do Processo
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 12px;">
                            <p style="margin: 6px 0; font-size: 14px; line-height: 1.4;">
                                <strong style="color: #1e293b;">Titular:</strong>
                                <span style="color: #475569;">{{ $complaint->full_name ?? 'N/D' }}</span>
                            </p>
                            <p style="margin: 6px 0; font-size: 14px; line-height: 1.4;">
                                <strong style="color: #1e293b;">Nº Apólice:</strong>
                                <span style="color: #475569;">{{ $complaint->policy_number ?? 'N/D' }}</span>
                            </p>
                            <p style="margin: 6px 0; font-size: 14px; line-height: 1.4;">
                                <strong style="color: #1e293b;">Entidade:</strong>
                                <span style="color: #475569;">{{ $complaint->entity ?? 'N/D' }}</span>
                            </p>
                            <p style="margin: 6px 0; font-size: 14px; line-height: 1.4;">
                                <strong style="color: #1e293b;">Data de Entrada:</strong>
                                <span
                                    style="color: #475569;">{{ optional($complaint->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-bottom: 24px;">
        <h4
            style="margin: 0 0 10px 0; font-size: 13px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.025em;">
            Resumo da sua Exposição:
        </h4>
        <div
            style="font-size: 14px; line-height: 1.6; color: #475569; font-style: italic; border-left: 4px solid #3b82f6; padding-left: 14px; background-color: #fafafa; padding-top: 8px; padding-bottom: 8px; border-radius: 0 6px 6px 0;">
            {!! html_entity_decode($complaint->description ?? 'Sem descrição fornecida.') !!}
        </div>
    </div>

    <p style="margin: 30px 0 0 0; font-size: 13px; color: #94a3b8; text-align: center; font-style: italic;">
        Poderá consultar actualizações sobre esta exposição acedendo diretamente ao nosso portal de reclamações.
    </p>
@endsection
