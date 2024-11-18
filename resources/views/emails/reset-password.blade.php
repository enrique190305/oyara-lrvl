<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <h2>Hola {{ $nombres }},</h2>
    
    <p>Has solicitado restablecer tu contraseña.</p>
    
    <p>Para continuar, haz clic en el siguiente botón:</p>
    
    <a href="{{ $resetUrl }}" class="button">Restablecer Contraseña</a>
    
    <p>O copia y pega el siguiente enlace en tu navegador:</p>
    <p>{{ $resetUrl }}</p>
    
    <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
    
    <div class="footer">
        <p>Este enlace expirará en 1 hora por motivos de seguridad.</p>
        <p>Si tienes problemas haciendo clic en el botón "Restablecer Contraseña", copia y pega la URL en tu navegador web.</p>
    </div>
</body>
</html>