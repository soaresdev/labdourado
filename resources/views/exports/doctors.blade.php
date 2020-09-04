<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url(mix('css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <title>Médicos</title>
</head>
<body>
<div class="mt-5">
    <div class="text-center">
        <img src="{{ asset('/images/logo.png') }}" alt="Logo MedAnalisys">
    </div>
    <h3 class="text-center my-3">Relatório de médicos cadastrados - {{ now()->format('d/m/Y') }}</h3>

    <table class="table table-bordered mb-5">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nome</th>
            <th scope="col">Nº no Conselho/UF</th>
            <th scope="col">CBO</th>
            <th scope="col">Cod. na Operadora</th>
            <th scope="col">Criado em</th>
            <th scope="col">Atualizado em</th>
        </tr>
        </thead>
        <tbody>
        @foreach($doctors as $doctor)
            <tr>
                <th scope="row">{{ $doctor->id }}</th>
                <td>{{ $doctor->name }}</td>
                <td>{{ $doctor->cp_formatted . ' - ' . $doctor->advice_number . '/' . $doctor->uf_formatted }}</td>
                <td>{{ $doctor->cbo }}</td>
                <td>
                    @foreach($doctors->flatMap->operators as $operator)
                        {{ $operator->name }} - {{ $operator->doctor_operator->doctor_operator_number }}
                        <br>
                    @endforeach
                </td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $doctor->created_at)->format('d/m/Y') }}</td>
                <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $doctor->updated_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
