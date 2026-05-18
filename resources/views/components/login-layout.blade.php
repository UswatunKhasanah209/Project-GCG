<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - eGCG INKA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: url('{{ asset("images/hero-inka.jpg") }}') no-repeat center center fixed;
        background-size: cover;
    }

    .top-bar {
        position: absolute;
        top: 30px;
        left: 40px;
        right: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .top-bar img {
        height: 65px;
    }

    /* 🔥 JUDUL DIPERBESAR */
    .welcome-text {
        text-align: center;
        margin-top: 130px;
        font-weight: 900;
        font-size: 48px;   /* ← DIBESARKAN */
        color: #000;
        letter-spacing: 1px;
    }

    .login-wrapper {
        min-height: 55vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* 🔥 BOX JADI TRANSPARAN GLASS */
    .login-card {
        background: rgba(255, 255, 255, 0.25); /* ← LEBIH TRANSPARAN */
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 45px;
        width: 420px;
        border-radius: 25px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        border: 1px solid rgba(255,255,255,0.3);
    }

    .login-title {
        text-align: center;
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 35px;
        color: #1f2937;
    }

    .login-btn {
        width: 100%;
        background-color: #2b7686;
        color: white;
        padding: 14px;
        border-radius: 8px;
        border: none;
        font-weight: 700;
        margin-top: 15px;
        transition: 0.3s;
    }

    .login-btn:hover {
        background-color: #1f5d6b;
    }

    input {
        border: 2px solid #2b7686 !important;
    }
</style>
</head>
<body>

<!-- LOGO ATAS -->
<div class="top-bar">
    <img src="{{ asset('images/Danantara.png') }}" alt="Danantara">
    <img src="{{ asset('images/Inka.png') }}" alt="INKA">
</div>

<!-- TEXT -->
<div class="welcome-text">
    Welcome to GCG Assessment System <br>
    PT Industri Kereta Api (Persero)
</div>

<!-- LOGIN CARD -->
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-title">Login</div>
        {{ $slot }}
    </div>
</div>

</body>
</html>