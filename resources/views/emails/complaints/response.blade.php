@extends('emails.layout')

@section('title', 'Resposta à sua Reclamação - Portal de Reclamações')

@section('protocolo', 'Protocolo: #' . ($response->complaint->code ?? 'N/D'))

@section('content')
    <h2 style="margin:0 0 16px 0; font-size:18px; font-weight:700; color:#3b1e7a;">
        Exmo(a). Sr(a). {{ $response->complaint->full_name }},
    </h2>
    
    <p style="margin:0 0 26px 0; font-size:16px; line-height:1.6; color:#475569;">
        Dando seguimento à sua exposição registada sob o protocolo <strong style="color: #9d1c7f;">#{{ $response->complaint->code }}</strong>, apresentamos abaixo a nossa resposta oficial:
    </p>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:30px; margin-bottom:35px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);">
        <div style="font-size:15px; line-height:1.8; color:#334155; white-space: pre-wrap;">
            {!! html_entity_decode($response->body ?? 'Sem descrição fornecida.') !!}
        </div>
    </div>

    <div style="margin-top:40px; border-top:1px solid #f1f5f9; padding-top:25px;">
        @if($response->signature_path && Storage::disk('public')->exists($response->signature_path))
            <div style="margin-bottom:12px; font-size:12px; color:#9d1c7f; text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">
                Assinado digitalmente por:
            </div>
            <img src="{{ $message->embed(storage_path("app/public/{$response->signature_path}")) }}" 
                 alt="Assinatura" 
                 style="width:260px; height:auto; display:block; filter: contrast(1.05);">
        @else
            <p style="margin:0; font-size:15px; color:#1e293b;"><strong>A Equipa de Qualidade</strong></p>
            <p style="margin:4px 0 0 0; font-size:14px; color:#94a3b8;">{{ App\Helpers\Helper::clean_app_name() }}</p>
        @endif
    </div>

    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
        <p style="margin:0; font-size:14px; color:#64748b; text-align:center; line-height:1.6; font-style: italic;">
            A sua opinião é fundamental para melhorarmos continuamente os nossos serviços.
        </p>
    </div>
@endsection