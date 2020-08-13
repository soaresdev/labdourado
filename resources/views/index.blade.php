<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ url(mix('css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/login.css')) }}">
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
