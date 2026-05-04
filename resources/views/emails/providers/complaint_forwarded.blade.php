<!doctype html>
<html lang="pt-PT">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova Reclamação Encaminhada</title>
  </head>
<body style="margin:0; padding:0; background: #eef2f7 linear-gradient(135deg, #eef2f7 0%, #dbeafe 100%); font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1e293b; min-height: 100vh;">

  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:50px 20px;">
        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px; background:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.1);">
          
          <tr>
            <td style="background: #0f172a; padding:45px 50px; text-align:center;">
              <div style="color: #3b82f6; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 2.5px; margin-bottom: 10px;">Gestão de Prestadores</div>
              <h1 style="margin:0; font-size:24px; font-weight:700; color:#ffffff; letter-spacing:-0.5px;">
                Nova Reclamação Encaminhada
              </h1>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding: 0;">
              <div style="display:inline-block; margin-top:-20px; background:#ffffff; border: 2px solid #3b82f6; color:#3b82f6; padding:6px 20px; border-radius:50px; font-size:13px; font-weight:700; box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                ID DO PROCESSO: #{{ $complaintProvider->complaint_id }}
              </div>
            </td>
          </tr>

          <tr>
            <td style="padding:40px 50px;">
              <p style="margin:0 0 20px 0; font-size:16px; color:#475569;">
                Prezado(a) <strong style="color:#0f172a;">{{ $complaintProvider->provider->name }}</strong>,
              </p>
              
              <p style="margin:0 0 30px 0; font-size:15px; line-height:1.7; color:#64748b;">
                Informamos que uma nova exposição foi encaminhada para a sua análise e acompanhamento obrigatório dentro do prazo estabelecido.
              </p>

              <div style="background:#f8fafc; border-radius:16px; padding:25px; border:1px solid #e2e8f0; margin-bottom:35px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td style="padding-bottom:15px;">
                      <div style="font-size:12px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Resumo do Encaminhamento</div>
                      
                      <div style="margin-bottom:10px; font-size:15px;">
                        <span style="color:#64748b;">Assunto:</span> <strong style="color:#1e293b;">{{ $complaintProvider->summary }}</strong>
                      </div>

                      @if($complaintProvider->deadline)
                      <div style="display:inline-block; background:#fef2f2; border:1px solid #fee2e2; color:#ef4444; padding:4px 12px; border-radius:6px; font-size:13px; font-weight:700;">
                        ⏱ Prazo limite: {{ $complaintProvider->deadline->format('d/m/Y') }}
                      </div>
                      @endif
                    </td>
                  </tr>
                  
                  @if($complaintProvider->notes)
                  <tr>
                    <td style="padding-top:15px; border-top:1px solid #e2e8f0;">
                      <div style="font-size:12px; font-weight:700; color:#1e293b; margin-bottom:5px;">Notas Internas:</div>
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
                    <p style="margin-bottom:20px; font-size:14px; color:#94a3b8;">Para consultar o processo completo e submeter a sua resposta:</p>
                    <a href="{{ config('app.url') }}" style="display:inline-block; padding:18px 45px; background-color:#0f172a; color:#ffffff; font-weight:700; font-size:15px; text-decoration:none; border-radius:12px; box-shadow:0 10px 20px rgba(15,23,42,0.2);">
                      Aceder ao Painel do Prestador
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="padding:40px 50px; background-color:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
              <p style="margin:0; font-size:13px; color:#94a3b8; line-height:1.6;">
                Atenciosamente,<br>
                <strong style="color:#64748b;">Equipa de Gestão de Qualidade</strong><br>
                {{ App\Helpers\Helper::clean_app_name() }}
              </p>
              <p style="margin-top:20px; font-size:11px; color:#cbd5e1; text-transform:uppercase;">
                Este é um envio automático • Não responda a este endereço
              </p>
            </td>
          </tr>

        </table>

        <p style="margin-top:25px; font-size:12px; color:#94a3b8; text-align:center;">
          © {{ date('Y') }} {{ App\Helpers\Helper::clean_app_name() }} Compliance
        </p>

      </td>
    </tr>
  </table>

</body>
</html>