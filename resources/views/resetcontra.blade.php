<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #818cf8;
            --bg-color: #111827;
            --card-bg: #1f2937;
            --input-bg: #374151;
            --text-color: #f3f4f6;
            --text-muted: #9ca3af;
            --border-color: #374151;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .container {
            margin: 2rem auto;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.3), 0 2px 4px -2px rgb(0 0 0 / 0.3);
            overflow: hidden;
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .card-header h3 {
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            text-align: center;
        }

        .card-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: var(--text-muted);
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            color: var(--text-color);
            font-size: 1rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: var(--input-bg);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-size: 1rem;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .alert ul {
            margin-bottom: 0;
            padding-left: 1.25rem;
        }

        .alert li {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            color: var(--text-muted);
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .input-group .form-control {
            padding-left: 2.75rem;
        }

        /* Efecto de brillo en hover para el botón */
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                45deg,
                transparent 0%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 100%
            );
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .btn-primary:hover::before {
            transform: translateX(100%);
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Recuperar Contraseña</h3>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('status') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group">
                                <label for="correo">Correo Electrónico</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" class="form-control" id="correo" name="correo" placeholder="nombre@ejemplo.com" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary position-relative overflow-hidden">
                                <i class="fas fa-paper-plane me-2"></i>
                                Enviar enlace de recuperación
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>