@extends('emails.layout')

@section('title', 'Notificação de Retificação')

@section('protocolo')
    ID #{{ $complaint->code ?? 'N/D' }}
@endsection

@section('content')
    <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a;">
        Exmo.(a) Sr.(a) {{ trim($analista->first_name ?? 'Analista') }},
    </h2>

    <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
        Os dados cadastrais da reclamação foram atualizados pelo reclamante. O processo encontra-se atualmente no estado
        Pendente, aguardando uma nova triagem.
    </p>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="margin-bottom: 24px; border-collapse: collapse;">
        <tr>
            <td style="padding: 14px; background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="padding: 4px 0; font-size: 14px; color: #0f172a;">
                            <strong>Reclamante:</strong> {{ $complaint->full_name ?? 'N/D' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; font-size: 14px; color: #0f172a;">
                            <strong>Apólice / Contrato:</strong> {{ $complaint->policy_number ?? 'N/D' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
