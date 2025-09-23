<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Notificação de Alerta - Nossa Seguros</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f0f4f9, #d9e4f5);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 520px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border: 2px solid #0072CE;
            position: relative;
        }
        .container::before {
            content: "";
            position: absolute;
            top: -6px;
            left: -6px;
            right: -6px;
            bottom: -6px;
            border-radius: 20px;
            background: linear-gradient(135deg, #003366, #0072CE);
            z-index: -1;
        }
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo img {
            max-width: 180px;
        }
        h2 {
            color: #003366;
            font-size: 28px;
            margin: 20px 0;
            letter-spacing: 2px;
            text-align: center;
            border: 2px dashed #0072CE;
            padding: 12px;
            border-radius: 10px;
            background: #f0f8ff;
        }
        p {
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
        }
        .info-box {
            margin: 20px 0;
            padding: 15px;
            border-radius: 10px;
            background: #f9fbff;
            border: 1px solid #d9e4f5;
        }
        .info-box p {
            margin: 6px 0;
        }
        .highlight {
            color: #0072CE;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #666666;
            text-align: center;
            border-top: 1px solid #e5e5e5;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- LOGO -->
        <div class="logo">
            <img src="https://www.nossaseguros.ao/assets/img/logo.png" alt="Nossa Seguros">
        </div>

        <p>Olá, <span class="highlight">{{ $user->first_name }}</span></p>
        <p>Identificámos um novo alerta associado à entidade:</p>
        
        <h2>{{ $alert->entity->social_denomination }}</h2>

        <div class="info-box">
         
            <p><strong>Nível:</strong> {{ $alert->level }}</p>
   
            <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($alert->created_at)->format('d/m/Y H:i') }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Nossa Seguros — Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
