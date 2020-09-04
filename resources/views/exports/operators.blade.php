<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url(mix('css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <title>Operadoras</title>
</head>
<body>
<div class="mt-5">
    <div class="text-center">
        <img src="{{ asset('/images/logo.png') }}" alt="Logo MedAnalisys">
    </div>
    <h3 class="text-center my-3">Relatório de operadoras/convênios cadastrados - {{ now()->format('d/m/Y') }}</h3>

    <table class="table table-bordered mb-5">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nome</th>
            <th scope="col">Registro ANS</th>
            <th scope="col">Qtd. Pacientes</th>
            <th scope="col">Qtd. Médicos</th>
            <th scope="col">Qtd. Procedimentos</th>
            <th scope="col">Qtd. Lotes</th>
            <th scope="col">Qtd. Guias SP/SADT</th>
            <th scope="col">Criado em</th>
            <th scope="col">Atualizado em</th>
        </tr>
        </thead>
        <tbody>
        @foreach($operators as $operator)
            <tr>
                <th scope="row">{{ $operator->id }}</th>
                <td>{{ $operator->name }}</td>
                <td>{{ $operator->ans }}</td>
                <td>{{ $operator->patients_count }}</td>
                <td>{{ $operator->doctors_count }}</td>
                <td>{{ $operator->procedures_count }}</td>
                <td>{{ $operator->lots_count }}</td>
                <td>{{ $operator->qtd_guides }}</td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $operator->created_at)->format('d/m/Y') }}</td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $operator->updated_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
