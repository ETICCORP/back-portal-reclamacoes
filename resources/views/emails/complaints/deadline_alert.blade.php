<!doctype html>
<html lang="pt-PT">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alerta de Prazo de Reclamação</title>
  </head>
<body style="margin:0; padding:0; background: #fff7ed linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1e293b; min-height: 100vh;">

  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:50px 20px;">
        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px; background:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.08);">
          
          <tr>
            <td style="background: #7c2d12; padding:40px 50px; text-align:center;">
              <div style="color: #fb923c; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">Aviso de Sistema • Urgente</div>
              <h1 style="margin:0; font-size:24px; font-weight:700; color:#ffffff; letter-spacing:-0.5px;">
                Alerta de Prazo Limite
              </h1>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding: 0;">
              <div style="display:inline-block; margin-top:-20px; background:#ea580c; color:#ffffff; padding:8px 24px; border-radius:50px; font-size:14px; font-weight:700; box-shadow:0 8px 15px rgba(234,88,12,0.3);">
                Restam {{ $deadline->remainingDays() }} dias
              </div>
            </td>
          </tr>

          <tr>
            <td style="padding:40px 50px;">
              <p style="margin:0 0 20px 0; font-size:16px; color:#475569;">
                Olá, verificamos uma pendência crítica no sistema:
              </p>
              
              <p style="margin:0 0 30px 0; font-size:15px; line-height:1.7; color:#1e293b;">
                A reclamação identificada pelo ID <strong style="color:#ea580c;">#{{ $deadline->complaint_id }}</strong> está prestes a atingir o seu limite legal para resposta.
              </p>

              <div style="background:#fffaf5; border:1px solid #ffedd5; border-radius:16px; padding:25px; margin-bottom:35px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td width="50%" style="padding-bottom:15px;">
                      <div style="font-size:11px; color:#9a3412; font-weight:800; text-transform:uppercase;">Status Atual</div>
                      <div style="font-size:15px; font-weight:600; color:#c2410c;">{{ $deadline->status }}</div>
                    </td>
                    <td width="50%" style="padding-bottom:15px;">
                      <div style="font-size:11px; color:#9a3412; font-weight:800; text-transform:uppercase;">Data de Início</div>
                      <div style="font-size:15px; color:#475569;">{{ $deadline->start_date->format('d/m/Y') }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="padding-top:15px; border-top:1px solid #ffedd5;">
                      <div style="font-size:11px; color:#9a3412; font-weight:800; text-transform:uppercase;">Data Limite (Deadline)</div>
                      <div style="font-size:18px; font-weight:700; color:#ea580c;">{{ $deadline->end_date->format('d/m/Y') }}</div>
                    </td>
                  </tr>
                </table>
              </div>

              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center">
                    <p style="margin-bottom:20px; font-size:14px; color:#64748b;">Aceda imediatamente para evitar incumprimentos:</p>
                    <a href="{{ url('/complaints/' . $deadline->complaint_id) }}" style="display:inline-block; padding:18px 45px; background-color:#ea580c; color:#ffffff; font-weight:700; font-size:15px; text-decoration:none; border-radius:12px; box-shadow:0 10px 20px rgba(234,88,12,0.2);">
                      Resolver Pendência Agora
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:40px 50px; background-color:#fffcf9; border-top:1px solid #ffedd5; text-align:center;">
              <p style="margin:0; font-size:13px; color:#94a3b8; line-height:1.6;">
                Atenciosamente,<br>
                <strong style="color:#7c2d12;">Gestão de Fluxos e Prazos</strong><br>
                {{ App\Helpers\Helper::clean_app_name() }}
              </p>
              <p style="margin-top:20px; font-size:11px; color:#cbd5e1; text-transform:uppercase;">
                Notificação Automática de Compliance
              </p>
            </td>
          </tr>

        </table>

        <p style="margin-top:25px; font-size:12px; color:#94a3b8; text-align:center;">
          © {{ date('Y') }} {{ App\Helpers\Helper::clean_app_name() }} • Monitorização de SLA
        </p>

      </td>
    </tr>
  </table>

</body>
</html>