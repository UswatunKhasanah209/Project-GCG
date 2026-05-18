<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            /* GUNAKAN BACKGROUND LOGIN SECARA DEFAULT */
            background-image: url('{{ asset("images/BG login.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* CARD */
        .auth-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 420px;
        }
    </style>

</head>

<body class="min-h-screen flex items-center justify-center">

    <div class="auth-card">
        {{ $slot }}
    </div>

</body>
</html>
