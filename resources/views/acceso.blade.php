<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Q'hubo</title>
    <link rel="stylesheet" href="{{ asset('css/estilos2.css') }}" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container {{ session('errors') ? 'sign-up-mode' : '' }}">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- Login Form -->
                <form method="POST" action="{{ route('procesarAcceso') }}" class="sign-in-form">
                    @csrf
                    <h2 class="title">Iniciar Sesión</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="email" name="email" placeholder="Email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Contraseña" required />
                    </div>
                    <div class="remember-forgot" style="width: 60%; margin-bottom: 10px;">
                        <label style="margin-right: 15px;"><input type="checkbox" name="remember"> Recordarme</label>
                        <a class="forgot-password" href="{{ url('resetcontra') }}">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input type="submit" value="Acceder" class="btn solid" />

                    <p class="social-text">O inicia sesión con</p>
                    <div class="social-media">
                        <a href="{{ route('social.login', ['provider' => 'google']) }}" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="{{ route('social.login', ['provider' => 'github']) }}" class="social-icon">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </form>

                <!-- Register Form -->
                <form method="POST" action="{{ route('registrarUsuario') }}" class="sign-up-form">
                    @csrf
                    <h2 class="title">Registrarse</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Usuario" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Contraseña" required />
                    </div>
                    <div style="width: 60%; margin-bottom: 10px;">
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" required style="margin-right: 10px;">
                            Acepto los términos y condiciones
                        </label>
                    </div>
                    <input type="submit" class="btn" value="Registrarse" />

                    <p class="social-text">O regístrate con</p>
                    <div class="social-media">
                        <a href="{{ route('social.login', ['provider' => 'google']) }}" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="{{ route('social.login', ['provider' => 'github']) }}" class="social-icon">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>¿Nuevo aquí?</h3>
                    <p>
                        ¡Únete a nuestra comunidad! Registrate para descubrir todas las
                        funcionalidades que tenemos para ti.
                    </p>
                    <button class="btn transparent" id="sign-up-btn">
                        Registrarse
                    </button>
                </div>
                <img src="{{ asset('img/log.svg') }}" class="image" alt="" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>¿Ya eres uno de nosotros?</h3>
                    <p>
                        Inicia sesión para acceder a tu cuenta y todas las funcionalidades
                        disponibles.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Iniciar Sesión
                    </button>
                </div>
                <img src="{{ asset('img/register.svg') }}" class="image" alt="" />
            </div>
        </div>

        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>