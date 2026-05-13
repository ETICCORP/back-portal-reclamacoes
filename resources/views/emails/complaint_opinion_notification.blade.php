<!doctype html>
<html lang="pt-PT">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parecer Técnico - Reclamação #{{ $complaint->code }}</title>
</head>

<body
    style="margin:0; padding:0; background-color:#f8fafc; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1e293b; -webkit-font-smoothing:antialiased;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding:40px 20px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width:640px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05);">

                    <tr>
                        <td style="background-color:#0f172a; padding:32px 40px; text-align:left;">
                            <h1
                                style="margin:0; font-size:22px; font-weight:700; color:#ffffff; letter-spacing:-0.02em;">
                                {{ App\Helpers\Helper::clean_app_name() }}
                            </h1>
                            <div
                                style="margin-top:8px; display:inline-block; padding:4px 12px; background:rgba(59, 130, 246, 0.2); border: 1px solid rgba(255,255,255,0.2); border-radius:20px; color:#ffffff; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">
                                Novo Parecer Técnico
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:40px 40px 30px 40px;">
                            <h2 style="margin:0 0 16px 0; font-size:20px; font-weight:600; color:#0f172a;">
                                Notificação de Atualização
                            </h2>
                            <p style="margin:0 0 24px 0; font-size:16px; line-height:1.6; color:#475569;">
                                Informamos que foi submetido um novo parecer técnico relativo ao processo de reclamação
                                identificado abaixo.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="margin-bottom:30px; border-collapse:collapse;">
                                <tr>
                                    <td
                                        style="padding:15px; background-color:#f8fafc; border-radius:8px; border:1px solid #f1f5f9;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                            width="100%">
                                            <tr>
                                                <td style="padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
                                                    <span
                                                        style="font-size:12px; color:#64748b; text-transform:uppercase; font-weight:700;">Identificação
                                                        do Processo</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top:12px;">
                                                    <p style="margin:4px 0; font-size:14px;"><strong
                                                            style="color:#1e293b;">Protocolo:</strong> <span
                                                            style="color:#3b82f6; font-weight:600;">#{{ $complaint->code }}</span>
                                                    </p>
                                                    <p style="margin:4px 0; font-size:14px;"><strong
                                                            style="color:#1e293b;">Titular:</strong> <span
                                                            style="color:#64748b;">{{ $complaint->full_name }}</span>
                                                    </p>
                                                    <p style="margin:4px 0; font-size:14px;"><strong
                                                            style="color:#1e293b;">Técnico Responsável:</strong> <span
                                                            style="color:#64748b;">{{ $opinion->user->fullName ?? 'N/A' }}</span>
                                                    </p>
                                                    <p style="margin:4px 0; font-size:14px;"><strong
                                                            style="color:#1e293b;">Data do Parecer:</strong> <span
                                                            style="color:#64748b;">{{ Carbon\Carbon::parse($opinion->submitted_at)->format('d/m/Y H:i') }}</span>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <div style="margin-bottom:32px;">
                                <h4
                                    style="margin:0 0 12px 0; font-size:14px; font-weight:700; color:#0f172a; text-transform:uppercase; letter-spacing:0.025em;">
                                    Parecer Técnico / Decisão:
                                </h4>
                                <div
                                    style="font-size:15px; line-height:1.6; color:#1e293b; background-color:#fffbeb; border-left:4px solid #f59e0b; padding:16px; border-radius: 0 8px 8px 0;">
                                    {!! html_entity_decode($opinion->opinion ?? 'Sem conteúdo descritivo.') !!}
                                </div>
                            </div>

                            {{-- <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}/complaints/{{ $complaint->id }}"
                                            style="display:inline-block; padding:14px 32px; background-color:#0f172a; color:#ffffff; font-weight:600; font-size:15px; text-decoration:none; border-radius:6px; box-shadow:0 4px 6px -1px rgba(15,23,42,0.3);">
                                            Visualizar Processo Completo
                                        </a>
                                    </td>
                                </tr>
                            </table> --}}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 40px; background-color:#f1f5f9; text-align:center;">
                            <p style="margin:0; font-size:13px; color:#94a3b8; line-height:1.5;">
                                Este é um e-mail de notificação automática do sistema de gestão.<br>
                                <strong>{{ App\Helpers\Helper::clean_app_name() }}</strong>
                            </p>
                            <div style="margin-top:16px; border-top:1px solid #e2e8f0; padding-top:16px;">
                                <p
                                    style="margin:0; font-size:11px; color:#cbd5e1; text-transform:uppercase; letter-spacing:0.05em;">
                                    © {{ date('Y') }} Portal de Reclamações. Todos os direitos reservados.
                                </p>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
