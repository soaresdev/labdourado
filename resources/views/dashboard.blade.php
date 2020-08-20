<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <!-- Web Application Manifest -->
    <link rel="manifest" href="{{ config('app.url') . '/manifest.json'  }}">
    <!-- Chrome for Android theme color -->
    <meta name="theme-color" content="{{ config('pwa.manifest.background_color')  }}">

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable"
          content="{{ config('pwa.manifest.display') == 'standalone' ? 'yes' : 'no' }}">
    <meta name="application-name" content="{{ config('pwa.manifest.short_name') }}">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable"
          content="{{ config('pwa.manifest.display') == 'standalone' ? 'yes' : 'no' }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ config('pwa.manifest.short_name') }}">
    <link rel="apple-touch-icon" href="{{ url(asset('images/icon/icon-144x144.png')) }}">

    <!-- Tile for Win8 -->
    <meta name="msapplication-TileColor" content="{{ config('pwa.manifest.background_color') }}">
    <meta name="msapplication-TileImage" content="{{ url(asset('images/icon/icon-144x144.png')) }}">

    <script type="text/javascript">
        // Initialize the service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js', {
                scope: '.'
            }).then(function (registration) {
                // Registration was successful
                console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
            }, function (err) {
                // registration failed :(
                console.log('Laravel PWA: ServiceWorker registration failed: ', err);
            });
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <link rel="shortcut icon" href="{{ url(asset('images/icon/favicon.ico')) }}" type="image/x-icon">
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
