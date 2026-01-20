<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--besame la paloma-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mara-Doors | Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --madera: #d2a679;
            --madera-oscura: #8b5a2b;
            --madera-clara: #f5e6d3;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url("{{ asset('/assets/img/chapita.jpg') }}") no-repeat center center fixed;
            background-size: cover;
            color: #2a1a0f;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.4);
            min-height: 100vh;
        }

        .navbar {
            background: var(--madera-oscura) !important;
        }

        .navbar-brand {
            font-weight: 800;
            color: #fff !important;
            text-shadow: 1px 1px 3px #000;
        }

        .nav-link {
            color: #f5e6d3 !important;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        .btn-madera {
            background: var(--madera-oscura);
            color: #fff;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .25);
            transition: .2s;
        }

        .btn-madera:hover {
            background: #5e3b1f;
            transform: translateY(-2px);
        }

        .hero {
            padding: 12vh 15px;
            text-align: center;
            color: #fff;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            text-shadow: 3px 3px 8px rgba(0, 0, 0, .7);
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, .6);
        }

        .section-light {
            background: var(--madera-clara);
        }

        .card-wood {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, .2);
        }

        footer {
            background: var(--madera-oscura);
            color: #fff;
            padding: 15px;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="overlay">

        <!-- Navbar -->
        <nav class="navbar navbar-expand-md shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">Mara-Doors</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ route('panel') }}">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="#acerca">Acerca de</a></li>
                    </ul>
                    <a class="btn btn-madera" href="{{ route('login') }}">Iniciar sesión</a>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <main class="hero">
            <h1>Mara-Doors</h1>
            <p>Sistema de ventas para tu negocio de puertas y carpintería.</p>
            <a class="btn btn-lg btn-madera" href="{{ route('login') }}">Comenzar</a>
        </main>
    </div>

    <!-- Sección Acerca -->
    <section id="acerca" class="section-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- Imagen -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <img src="/assets/img/lapacho.jpg"
                        class="img-fluid rounded shadow-sm" alt="Mara-Doors - Madera">
                </div>
                <!-- Texto -->
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">¿Quiénes somos?</h2>
                    <p class="mb-3 text-muted">
                        En <strong>Mara-Doors</strong> combinamos la nobleza de la madera.
                        Creamos una plataforma para carpinteros y fabricantes que necesitan controlar su inventario,
                        ventas y clientes de manera simple y eficiente. Espero que le guste.
                    </p>
                    <ul class="list-unstyled text-muted">
                        <li>✔ Soporte técnico personalizado al famoso rey</li>
                    </ul>
                    <a class="btn btn-madera" href="{{ route('login') }}">Empieza ahora</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        © {{ date('Y') }} Mara-Doors – Hecho con ❤️ para carpinteros
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
