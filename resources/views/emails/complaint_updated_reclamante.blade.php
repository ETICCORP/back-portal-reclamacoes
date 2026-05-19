@extends('emails.layout')

@section('title', 'Dados Actualizados com Sucesso')

@section('protocolo')
    ID #{{ $complaint->code ?? 'N/D' }}
@endsection

@section('content')
    <h2 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 700; color: #0f172a;">
        Olá, {{ explode(' ', trim($complaint->full_name ?? 'Reclamante'))[0] }}.
    </h2>

    <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #475569;">
        Confirmamos que os dados da sua exposição foram actualizados com sucesso. O seu processo foi reencaminhado para a nossa equipa de analistas para uma nova triagem técnica.
    </p>

    <p style="margin: 30px 0 0 0; font-size: 13px; color: #94a3b8; text-align: center; font-style: italic;">
        Poderá acompanhar o andamento desta nova fase directamente através do portal.
    </p>
@endsection