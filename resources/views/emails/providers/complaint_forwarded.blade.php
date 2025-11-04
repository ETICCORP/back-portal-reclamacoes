<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Nova Reclamação Encaminhada</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f8fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            max-width: 720px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .email-header {
            background-color: #004aad;
            color: #fff;
            padding: 20px 40px;
            text-align: center;
        }

        .email-header h2 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.5px;
        }

        .email-body {
            padding: 30px 40px;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #f0f4ff;
            margin-bottom: 8px;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14.5px;
        }

        li strong {
            color: #004aad;
        }

        .footer {
            border-top: 1px solid #e5e5e5;
            margin-top: 25px;
            padding-top: 15px;
            font-size: 13px;
            color: #777;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h2>Nova Reclamação Encaminhada</h2>
        </div>

        <div class="email-body">
            <p>Olá <strong>{{ $complaintProvider->provider->name }}</strong>,</p>

            <p>Uma nova reclamação foi encaminhada para o seu acompanhamento. Seguem os detalhes abaixo:</p>

            <ul>
                <li><strong>ID da Reclamação:</strong> {{ $complaintProvider->complaint_id }}</li>
                <li><strong>Resumo:</strong> {{ $complaintProvider->summary }}</li>

                @if($complaintProvider->notes)
                    <li><strong>Notas Internas:</strong> {{ $complaintProvider->notes }}</li>
                @endif

                @if($complaintProvider->deadline)
                    <li><strong>Prazo para Resposta:</strong> {{ $complaintProvider->deadline->format('d/m/Y') }}</li>
                @endif
            </ul>

            <p>Por favor, aceda ao sistema para mais informações ou para responder a esta reclamação.</p>

            <div class="footer">
                <p>Atenciosamente,<br><strong>Equipe de Reclamações</strong></p>
                <p>Este é um e-mail automático enviado por {{ config('app.name') }}.<br>Por favor, não responda diretamente a esta mensagem.</p>
            </div>
        </div>
    </div>
</body>
</html>
