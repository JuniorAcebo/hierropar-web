<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Inicio de sesión del sistema" />
    <meta name="author" content="SakCode" />
    <title>Sistema de ventas - Login</title>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: url("{{ asset('/assets/img/chapita.jpg') }}") no-repeat center center fixed;
            background-size: cover;

        }

        .overlay {
            background: rgba(0, 0, 0, 0.55);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            max-width: 400px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
            padding: 30px;
            animation: fadeIn 0.7s ease-in-out;
        }

        .card h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #5e3b1f;
        }

        .alert {
            background: #ffe5e5;
            color: #a00;
            border-left: 5px solid #e74c3c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #5e3b1f;
            font-size: 0.95rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 0px;
            border: 2px solid #d2a679;
            border-radius: 8px;
            font-size: 1rem;
            background: #fdfaf6;
            transition: 0.25s;
        }

        input:focus {
            border-color: #8b5a2b;
            outline: none;
            box-shadow: 0 0 6px rgba(139, 90, 43, 0.5);
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            background: linear-gradient(135deg, #8b5a2b, #d2a679);
            color: #fff;
            transition: 0.3s;
        }

        .btn:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85rem;
            color: #eee;
        }

        footer a {
            color: #d2a679;
            text-decoration: none;
            margin: 0 5px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="overlay">
        <div class="card">
            <h3>Acceso al sistema</h3>

            @if ($errors->any())
                @foreach ($errors->all() as $item)
                    <div class="alert">{{ $item }}</div>
                @endforeach
            @endif

            <form action="{{ route('login') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="inputEmail">Correo electrónico</label>
                    <input autofocus autocomplete="off" value="invitado@gmail.com" name="email" id="inputEmail"
                        type="email" placeholder="name@example.com" />
                </div>

                <div class="form-group">
                    <label for="inputPassword">Contraseña</label>
                    <input name="password" value="12345678" id="inputPassword" type="password" placeholder="Password" />
                </div>

                <button class="btn" type="submit">Iniciar sesión</button>
            </form>

            <footer>
                <div>Copyright &copy; Your Website 2022</div>
                <div>
                    <a href="#">Privacy Policy</a> ·
                    <a href="#">Terms &amp; Conditions</a>
                </div>
            </footer>
        </div>
    </div>
</body>

</html>


