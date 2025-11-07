<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #eef2f7;
      color: #333;
      margin: 0;
      padding: 40px 0;
    }
    .container {
      max-width: 700px;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 14px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.08);
      overflow: hidden;
    }
    .header {
      background: linear-gradient(135deg, #004aad, #0073e6);
      padding: 25px 40px;
      color: #ffffff;
    }
    .header h1 {
      margin: 0;
      font-size: 22px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    .body {
      padding: 35px 45px;
    }
    .body p {
      font-size: 15px;
      line-height: 1.7;
      margin-bottom: 16px;
    }
    .highlight {
      background-color: #f5f8ff;
      border-left: 4px solid #004aad;
      padding: 14px 18px;
      border-radius: 6px;
      font-size: 15px;
      color: #222;
      margin-top: 12px;
      margin-bottom: 22px;
    }
    .signature {
      margin-top: 35px;
      border-top: 1px solid #e6e9f0;
      padding-top: 18px;
      font-size: 14px;
      color: #555;
    }
    .signature strong {
      color: #004aad;
      font-weight: 600;
    }
    .signature img {
      margin-top: 10px;
      width: 160px;
      border-radius: 4px;
    }
    .footer {
      background-color: #f9fafc;
      text-align: center;
      padding: 18px;
      font-size: 13px;
      color: #777;
      border-top: 1px solid #e6e9f0;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>{{ config('app.name') }}</h1>
    </div>

    <div class="body">
      <p>Exmo(a). <strong>{{ $response->complaint->full_name }}</strong>,</p>

      <div class="highlight">
        {!! nl2br(e($response->body)) !!}
      </div>

      <div class="signature">
      
        @if($response->signature_path)
          <img src="http://172.17.100.11:1121/storage/{{ $response->signature_path }}" alt="Assinatura" width="200">
        @endif
      </div>
    </div>

    <div class="footer">
      Esta mensagem foi enviada automaticamente por <strong>{{ config('app.name') }}</strong>.<br>
      Por favor, não responda a este e-mail.
    </div>
  </div>
</body>
</html>
