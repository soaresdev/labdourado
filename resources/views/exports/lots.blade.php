<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url(mix('css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <title>Lotes</title>
</head>
<body>
<div class="mt-5">
    <div class="text-center">
        <img src="{{ asset('/images/logo.png') }}" alt="Logo MedAnalisys">
    </div>
    <h3 class="text-center my-3">Relatório de lotes cadastrados - {{ now()->format('d/m/Y') }}</h3>

    <table class="table table-bordered mb-5">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nº do Lote</th>
            <th scope="col">Operadora</th>
            <th scope="col">Qtd. Guias</th>
            <th scope="col">Total do Lote</th>
            <th scope="col">Fechado em</th>
            <th scope="col">Criado em</th>
            <th scope="col">Atualizado em</th>
        </tr>
        </thead>
        <tbody>
        @foreach($lots ?? '' as $lot)
            <tr>
                <th scope="row">{{ $lot->id }}</th>
                <td>{{ $lot->number }}</td>
                <td>{{ $lot->operator->name }}</td>
                <td>{{ $lot->guides_count }}</td>
                <td>{{ $lot->total_formatted }}</td>
                <td>{{ $lot->closed_at_formatted }}</td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $lot->created_at)->format('d/m/Y') }}</td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $lot->updated_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
