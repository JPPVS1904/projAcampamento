<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Acampamento</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05); 
            animation: float 10s infinite linear;
        }

        .shape1 { width: 400px; height: 400px; top: -150px; left: -150px; }
        .shape2 { width: 500px; height: 500px; bottom: -200px; right: -150px; animation-duration: 15s; }
        .shape3 { width: 200px; height: 200px; top: 30%; left: 15%; animation-duration: 8s; }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        .login-container {
            background: #ffffff;
            padding: 3.5rem 3rem;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-area h2 {
            color: #1e3c72;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo-area p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group label {
            display: block;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .input-group input {
            width: 100%;
            padding: 1.1rem 1.5rem;
            background: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 10px;
            color: #212529;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input:focus {
            background: #fff;
            border-color: #2a5298;
            box-shadow: 0 0 0 4px rgba(42, 82, 152, 0.1);
        }

        .input-group input::placeholder {
            color: #adb5bd;
        }

        .btn-login {
            width: 100%;
            padding: 1.1rem;
            background: #1e3c72;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            box-shadow: 0 4px 6px rgba(30, 60, 114, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(30, 60, 114, 0.3);
            background: #2a5298;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            color: #495057;
            cursor: pointer;
        }

        .remember-me input {
            margin-right: 0.5rem;
            cursor: pointer;
        }

        .options a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .options a:hover {
            color: #2a5298;
            text-decoration: underline;
        }

        .signup-link {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .signup-link a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: 600;
            margin-left: 0.5rem;
            transition: opacity 0.3s ease;
        }

        .signup-link a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="bg-shape shape1"></div>
    <div class="bg-shape shape2"></div>
    <div class="bg-shape shape3"></div>

    <div class="login-container">
        <div class="logo-area">
            <h2>Acampamento</h2>
            <p>Faça login para acessar o sistema</p>
        </div>

        <form action="#" method="POST">
            @csrf
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required>
            </div>
            
            <div class="input-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Salvar dados
                </label>
                <a href="#">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="btn-login">Entrar no sistema</button>
        </form>

        <div class="signup-link">
            Não tem uma conta? <a href="#">Cadastre-se</a>
        </div>
    </div>
</body>
</html>
