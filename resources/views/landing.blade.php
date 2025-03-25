<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a DripLineSoft</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .bg-primary { background-color: #8a8066 !important; }
        .bg-secondary { background-color: #4f6457 !important; }
        .bg-accent { background-color: #e3a68d !important; }
        .text-primary { color: #8a8066 !important; }
        .text-secondary { color: #4f6457 !important; }
        .text-accent { color: #e3a68d !important; }

        .btn-square {
            border-radius: 0;
            padding: 10px 25px;
            margin-left: 10px;
        }

        .btn-login {
            background-color: #4f6457;
            color: #fff;
            border: none;
        }

        .btn-register {
            background-color: #e3a68d;
            color: #fff;
            border: none;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-img {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow fade-in">
        <a class="navbar-brand" href="#">DripLineSoft</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link btn btn-login btn-square" href="/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-register btn-square" href="/register">Registro</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-primary text-white text-center py-5 fade-in">
        <div class="container">
            <h1 class="display-4">Bienvenido a DripLineSoft</h1>
            <p class="lead">Impulsamos tu negocio digitalizando tus productos y servicios.</p>
        </div>
    </section>

    <!-- Información de la Empresa -->
    <section class="py-5 fade-in">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="text-primary">¿Quiénes somos?</h2>
                    <p>DripLineSoft es una empresa dedicada a ayudar a negocios a crecer en el mundo digital. Nuestro sistema permite registrar tu negocio, agregar tus productos y gestionar pedidos de forma eficiente.</p>
                    <p>Creemos en el poder de la digitalización para que tu empresa sea más visible y accesible para los clientes.</p>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('img/driplinesoft.webp') }}" class="img-fluid rounded shadow-lg hero-img" alt="DripLineSoft">
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section class="bg-secondary text-white py-5 fade-in">
        <div class="container">
            <h2 class="text-center mb-4">Nuestros Servicios</h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="card bg-accent text-white shadow">
                        <div class="card-body">
                            <i class="fas fa-users display-3 mb-3"></i>
                            <h5 class="card-title">Registro de usuarios</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white shadow">
                        <div class="card-body">
                            <i class="fas fa-store display-3 mb-3"></i>
                            <h5 class="card-title">Registro de negocios</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-accent text-white shadow">
                        <div class="card-body">
                            <i class="fas fa-shopping-cart display-3 mb-3"></i>
                            <h5 class="card-title">Gestión de productos</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white text-center py-3 fade-in">
        <p>&copy; 2025 DripLineSoft - Todos los derechos reservados</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
