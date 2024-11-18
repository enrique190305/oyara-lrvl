<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --bg-dark: #111827;
            --card-bg: #1f2937;
            --input-bg: #374151;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .container {
            padding: 2rem 1rem;
        }

        .card {
            background-color: var(--card-bg);
            border: none;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .card-header {
            background-color: rgba(0, 0, 0, 0.2);
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            background-color: var(--input-bg);
            color: var(--text-primary);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: var(--input-bg);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.25);
            color: var(--text-primary);
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #10b981;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #ef4444;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
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
                        <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="correo" value="{{ $correo }}">

                            <div class="form-group">
                                <label for="password">
                                    <i class="fas fa-key me-2"></i>Nueva Contraseña
                                </label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">
                                    <i class="fas fa-check-double me-2"></i>Confirmar Contraseña
                                </label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control" id="password_confirmation" 
                                           name="password_confirmation" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn">
                                <i class="fas fa-save me-2"></i>Cambiar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>