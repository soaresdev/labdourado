<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url(mix('css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <title>Pacientes</title>
</head>
<body>
<div class="mt-5">
    <div class="text-center">
        <img src="{{ asset('/images/logo.png') }}" alt="Logo MedAnalisys">
    </div>
    <h3 class="text-center my-3">Relatório de pacientes cadastrados - {{ now()->format('d/m/Y') }}</h3>

    <table class="table table-bordered mb-5">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nome</th>
            <th scope="col">Nº CNS</th>
            <th scope="col">Carteiras - Validade</th>
            <th scope="col">Criado em</th>
            <th scope="col">Atualizado em</th>
        </tr>
        </thead>
        <tbody>
        @foreach($patients as $patient)
            <tr>
                <th scope="row">{{ $patient->id }}</th>
                <td>{{ $patient->name }}</td>
                <td>{{ $patient->cns }}</td>
                <td>
                    @foreach($patients->flatMap->operators as $operator)
                        {{ $operator->name }}: {{ $operator->patient_operator->wallet_number }}
                        {{ $operator->patient_operator->wallet_expiration ? ' - ' . $operator->patient_operator->wallet_expiration_formatted : '' }}
                        <br>
                    @endforeach
                </td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $patient->created_at)->format('d/m/Y') }}</td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $patient->updated_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
