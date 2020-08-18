<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link rel="apple-touch-icon" href="{{ url(asset('assets/images/icon/icon-144x144.png')) }}">

    <!-- Tile for Win8 -->
    <meta name="msapplication-TileColor" content="{{ config('pwa.manifest.background_color') }}">
    <meta name="msapplication-TileImage" content="{{ url(asset('assets/images/icon/icon-144x144.png')) }}">

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
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ url(mix('css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/login.css')) }}">
    <link rel="shortcut icon" href="{{ url(asset('images/icon/favicon.ico')) }}" type="image/x-icon">
</head>

<body>
    <div class="dash_login">
        <div class="dash_login_left">
            <article class="dash_login_left_box">
                <header class="dash_login_box_headline">
                    <div class="dash_login_box_headline_logo icon-imob icon-notext"></div>
                    <h1>Login</h1>
                </header>
                <div class="errors"></div>
                <form name="login" id="login" action="{{ route('login') }}" method="post" autocomplete="off">
                    <label>
                        <span class="field icon-envelope">Usuário:</span>
                        <input type="text" name="username" placeholder="Informe seu usuário" required />
                    </label>

                    <label>
                        <span class="field icon-unlock-alt">Senha:</span>
                        <input type="password" name="password" placeholder="Informe sua senha" />
                    </label>

                    <button class="gradient gradient-blue radius icon-sign-in" type="button" id="submit">Entrar</button>
                </form>

                <footer>
                    <p>Desenvolvido por <a href="mailto:lucasoaresgomes@gmail.com?subject=Suporte">SoaresDev</a></p>
                    <p>&copy; <?= date("Y"); ?></p>
                    <p class="dash_login_left_box_support">
                        <a target="_blank" class="icon-whatsapp transition text-green"
                            href="https://api.whatsapp.com/send?phone=+55+031+998201535&text=Olá, preciso de ajuda com o login.">Precisa
                            de Suporte?</a>
                    </p>
                </footer>
            </article>
        </div>

        <div class="dash_login_right"></div>

    </div>
    <script type="text/javascript" src="{{ url(mix('js/login.js')) }}"></script>
</body>

</html>
