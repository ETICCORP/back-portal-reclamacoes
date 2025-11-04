<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9fb;
            color: #333;
            margin: 0;
            padding: 40px 0;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.05);
            padding: 30px 40px;
        }
        h2 {
            color: #004aad;
            margin-bottom: 20px;
            font-weight: 600;
        }
        p {
            line-height: 1.6;
            font-size: 15px;
        }
        .signature {
            margin-top: 35px;
            border-top: 1px solid #e5e5e5;
            padding-top: 15px;
            font-size: 14px;
            color: #555;
        }
        .signature img {
            margin-top: 10px;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Exmo(a). <strong>{{ $response->complaint->full_name }}</strong>,</p>

        <p>{!! nl2br(e($response->body)) !!}</p>

        <div class="signature">
            <p>Atenciosamente,<br>
            <strong>{{ $response->user->name }}</strong><br>
            {{ config('app.name') }}</p>

            @if($response->signature_path)
                <img src="{{ asset('storage/' . $response->signature_path) }}" alt="Assinatura">
            @endif
        </div>
    </div>
</body>
</html>
