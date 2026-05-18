<!doctype html>
<html lang="pt-PT">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Notificação - Portal de Reclamações')</title>
</head>

<body style="margin:0; padding:0; background-color:#f8fafc; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1e293b; -webkit-font-smoothing:antialiased;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding:40px 20px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 25px -5px rgba(59,30,122,0.08), 0 8px 10px -6px rgba(59,30,122,0.08);">

                    <tr>
                        <td style="background: #3b1e7a; background: linear-gradient(135deg, #3b1e7a 0%, #9d1c7f 50%, #e61575 100%); padding:35px 40px; text-align:left;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <h1 style="margin:0; font-size:24px; font-weight:800; color:#ffffff; letter-spacing:-0.03em;">
                                            Portal de Reclamações
                                        </h1>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        @hasSection('protocolo')
                                            <div style="padding:5px 14px; background:rgba(255,255,255,0.15); border-radius:20px; color:#ffffff; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; display:inline-block;">
                                                @yield('protocolo')
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:45px 40px 35px 40px;">
                            @yield('content')
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 40px; background-color:#f1f5f9; text-align:center; border-top: 1px solid #e2e8f0;">
                            <p style="margin:0; font-size:13px; color:#64748b; line-height:1.5; font-weight: 500;">
                                Este é um e-mail automático enviado pela plataforma, por favor não responda diretamente.<br>
                            </p>
                            <div style="margin-top:16px; border-top:1px solid #e2e8f0; padding-top:16px;">
                                <p style="margin:0; font-size:11px; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em;">
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