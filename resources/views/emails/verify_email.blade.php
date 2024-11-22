<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <style>
        /* Estilos básicos para mejorar la presentación */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 10px 0;
        }
        .content {
            margin-top: 20px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Verifica tu cuenta</h2>
        </div>
        <div class="content">
            <p>Hola, {{ $user->names }}.</p>
            <p>Gracias por registrarte. Para completar el registro de tu cuenta, confirma tu correo electrónico haciendo clic en el siguiente botón:</p>
            
            <a href="{{ url('verificar-cuenta/' . $token) }}" class="btn">Verificar mi cuenta</a>
            
            <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
            <p><a href="{{ url('verificar-cuenta' . $token) }}">{{ url('verificar-cuenta/' . $token) }}</a></p>
            
            <p>Si no realizaste este registro, puedes ignorar este mensaje.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
