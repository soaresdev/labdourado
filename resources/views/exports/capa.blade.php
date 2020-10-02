<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url(mix('css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <style type="text/css">
        table { page-break-inside:auto }
        tr { page-break-inside:avoid; page-break-after:auto }
    </style>
    <title>Capa de Lote</title>
</head>
<body>
<div class="mt-5">
    <div class="text-center">
        <img src="{{ asset('/images/logo.png') }}" alt="Logo MedAnalisys">
    </div>
    <h3 class="text-center my-3">Capa de Lote - Faturamento Eletrônico</h3>

    <h4 class="my-3"><b>Lote:</b> {{ $lot->number }}</h4>
    <h4 class="my-3"><b>Qtd. Guias:</b> {{ $lot->guides_count }}</h4>
    <h4 class="my-3"><b>Valor Total do Lote:</b> {{ $lot->total_formatted }}</h4>
    <table class="table table-bordered mb-5">
        <thead>
        <tr>
            <th scope="col">Beneficiário</th>
            <th scope="col">Guia</th>
            <th scope="col">Senha</th>
            <th scope="col">Data de Realização</th>
            <th scope="col">Qtd. de Procedimentos</th>
            <th scope="col">Valor Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($lot->guides as $guide)
            <tr>
                <td>{{ $guide->patient->name }}</td>
                <td>{{ $guide->provider_number }}</td>
                <td>{{ $guide->password }}</td>
                <td>{{ $guide->procedures->first()->guide_procedure->execution_date_formatted }}</td>
                <td>{{ $guide->procedures->count() }}</td>
                <td>{{ $guide->total_formatted }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="text-center">
        <input type="text" style="border: 0; border-bottom: 1px solid #000;">
        <p class="my-2"><b>Laboratório de Análises Clínicas Dourado LTDA | 26.605.578/0001-65</b><br><b>CNES:</b> 9629130</p>
        <p>Florestal - MG, {{ now()->formatLocalized('%d de %B de %Y') }}</p>
    </div>
</div>
</body>
</html>
