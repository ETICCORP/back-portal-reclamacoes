<!doctype html>
<html lang="pt-PT">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Resumo da Reclamação</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color:#333333;">

  <!-- Container central -->
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:30px 10px;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="max-width:600px; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.06);">

          <!-- Header -->
          <tr>
            <td style="background: linear-gradient(90deg,#0f62fe,#3b82f6); padding:20px 30px; color:#ffffff;">
              <h1 style="margin:0; font-size:20px; font-weight:600;">{{ config('app.name') }}</h1>
              <p style="margin:6px 0 0; font-size:13px; opacity:0.95;">Confirmação da Reclamação — Código: <strong>{{ $complaint->code }}</strong></p>
            </td>
          </tr>

          <!-- Corpo -->
          <tr>
            <td style="padding:24px 30px;">
              <p style="margin:0 0 14px; font-size:15px;">
                Caro(a) <strong>{{ optional($complaint->reporter)->full_name ?? 'Cliente' }}</strong>,
              </p>

              <p style="margin:0 0 18px; font-size:14px; color:#555;">
                Recebemos a sua reclamação. Segue o resumo do registo — guarde este e-mail para referência.
              </p>

              <!-- Card com dados -->
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:16px; border:1px solid #eef2f6; border-radius:6px;">
                <tr>
                  <td style="padding:16px;">
                    <h3 style="margin:0 0 10px; font-size:16px; color:#0f172a;">Dados da Reclamação</h3>

                    <p style="margin:6px 0; font-size:14px;"><strong>Código:</strong> {{ $complaint->code }}</p>
                    <p style="margin:6px 0; font-size:14px;"><strong>Reclamante / Contrato:</strong> {{ $complaint->contract_number ?? 'N/D' }}</p>
                    <p style="margin:6px 0; font-size:14px;"><strong>Email:</strong> {{ optional($complaint->reporter)->email ?? ($complaint['reporter']['email'] ?? 'N/D') }}</p>
                    <p style="margin:6px 0; font-size:14px;"><strong>Contacto:</strong> {{ optional($complaint->reporter)->phone ?? ($complaint['reporter']['phone'] ?? 'N/D') }}</p>
                    <p style="margin:6px 0; font-size:14px;"><strong>Tipologia:</strong> {{ $complaint->type ?? 'N/D' }}</p>
                    <p style="margin:6px 0; font-size:14px;"><strong>Entidade:</strong> {{ $complaint->entity ?? 'N/D' }}</p>
                    <p style="margin:6px 0 0; font-size:14px;"><strong>Qualidade:</strong> {{ optional($complaint->reporter)->quality ?? 'N/D' }}</p>
                  </td>
                </tr>
              </table>

              <!-- Descrição -->
              <div style="margin-top:18px; padding:14px; background:#fafbfc; border-radius:6px; border:1px solid #eef2f6;">
                <h4 style="margin:0 0 8px; font-size:15px; color:#0f172a;">Descrição</h4>
                <p style="margin:0; font-size:14px; color:#444; line-height:1.5;">{{ $complaint->description ?? 'Sem descrição fornecida.' }}</p>
              </div>

              <!-- Info adicional -->
              <p style="margin:18px 0 6px; font-size:13px; color:#6b7280;">
                <strong>Data de criação:</strong>
                {{ optional($complaint->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
              </p>

              <!-- Botão -->
              <p style="margin:18px 0;">
                <a href="{{ url('/complaints/' . $complaint->id) }}" style="display:inline-block; padding:10px 18px; border-radius:6px; text-decoration:none; font-weight:600; background:#0f62fe; color:#ffffff;">
                  Ver Reclamação
                </a>
              </p>

              <p style="margin:18px 0 0; font-size:13px; color:#6b7280;">
                Iremos contactá-lo(a) assim que houver uma atualização. Para questões urgentes responda a este e-mail.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f7f9fb; padding:16px 30px; text-align:center; color:#8892a6; font-size:12px;">
              <div style="margin-bottom:6px;">{{ config('app.name') }} • {{ config('app.url') ?? request()->getHost() }}</div>
              <div>© {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</div>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
