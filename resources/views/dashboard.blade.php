<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
</head>
<body>
    <div id="dashboard">
        <app></app>
    </div>
    <script>
        window.Dashboard = @json($scripts);
    </script>
    <script type="text/javascript" src="{{ url(mix('js/app.js')) }}"></script>
</body>
</html>
