<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Alerta de Prazo de Reclamação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #eee;
            padding: 20px;
            border-radius: 6px;
        }
        h2 {
            color: #d9534f;
        }
        p {
            line-height: 1.5;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Alerta de Prazo de Reclamação</h2>

        <p>Olá,</p>

        <p>O prazo para a reclamação com ID <strong>{{ $deadline->complaint_id }}</strong> está prestes a expirar.</p>

        <ul>
            <li><strong>Status:</strong> {{ $deadline->status }}</li>
            <li><strong>Dias restantes:</strong> {{ $deadline->remainingDays() }}</li>
            <li><strong>Data de início:</strong> {{ $deadline->start_date->format('d/m/Y') }}</li>
            <li><strong>Data limite:</strong> {{ $deadline->end_date->format('d/m/Y') }}</li>
        </ul>

        <p>Por favor, tome as medidas necessárias para garantir que a reclamação seja resolvida dentro do prazo legal.</p>

        <p>Atenciosamente,<br>Equipe de Reclamacoes</p>

        <div class="footer">
            Este é um e-mail automático, por favor não responda.
        </div>
    </div>
</body>
</html>
