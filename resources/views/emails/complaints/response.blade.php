<!doctype html>
<html lang="pt-PT">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resposta à sua Reclamação</title>
  </head>
<body style="margin:0; padding:0; background: #eef2f7 linear-gradient(135deg, #eef2f7 0%, #dbeafe 100%); font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1e293b; min-height: 100vh;">

  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:50px 20px;">
        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:680px; background:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,0.1);">
          
          <tr>
            <td style="background: #0f172a; padding:45px 50px; text-align:left;">
              <h1 style="margin:0; font-size:22px; font-weight:700; color:#ffffff; letter-spacing:-0.5px;">
                {{ App\Helpers\Helper::clean_app_name() }}
              </h1>
              <div style="margin-top:10px; display:inline-block; background:rgba(59,130,246,0.2); color:#60a5fa; padding:4px 12px; border-radius:6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
                Atualização do Processo
              </div>
            </td>
          </tr>

          <tr>
            <td style="padding:45px 50px;">
              <p style="margin:0 0 20px 0; font-size:17px; color:#1e293b;">
                Exmo(a). Sr(a). <strong>{{ $response->complaint->full_name }}</strong>,
              </p>
              
              <p style="margin:0 0 30px 0; font-size:16px; line-height:1.7; color:#64748b;">
                Dando seguimento à sua exposição registrada sob o protocolo <strong>#{{ $response->complaint->code }}</strong>, apresentamos abaixo a nossa resposta oficial:
              </p>

              <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:16px; padding:35px; margin-bottom:40px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size:16px; line-height:1.8; color:#334155; white-space: pre-wrap;">
                    {!! html_entity_decode($response->body ?? 'Sem descrição fornecida.') !!}
                  
              </div>
              </div>

              <div style="margin-top:40px; border-top:1px solid #f1f5f9; padding-top:30px;">
                @if($response->signature_path && Storage::disk('public')->exists($response->signature_path))
                  <div style="margin-bottom:10px; font-size:13px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; font-weight:700;">Assinado digitalmente por:</div>
                  <img src="{{ $message->embed(storage_path("app/public/{$response->signature_path}")) }}" 
                       alt="Assinatura" 
                       style="width:280px; height:auto; display:block; filter: contrast(1.1);">
                @else
                  <p style="margin:0; font-size:15px; color:#1e293b;"><strong>A Equipa de Qualidade</strong></p>
                  <p style="margin:4px 0 0 0; font-size:14px; color:#94a3b8;">{{ App\Helpers\Helper::clean_app_name() }}</p>
                @endif
              </div>
            </td>
          </tr>

          <tr>
            <td style="padding:40px 50px; background-color:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
              <p style="margin:0 0 20px 0; font-size:14px; color:#64748b;">
                A sua opinião é fundamental para melhorarmos os nossos serviços.
              </p>
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td align="center">
                    <p style="font-size:11px; color:#cbd5e1; text-transform:uppercase; letter-spacing:1px;">
                      Mensagem Automática • Gestão de Qualidade • {{ date('Y') }}
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>
</html>