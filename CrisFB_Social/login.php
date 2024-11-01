<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #3a77fc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" name="login">Iniciar Sesión</button>
        </form>
        <button onclick="window.location.href='register.html'">Registrarse</button>
        <?php
        if (isset($_POST['login'])) {
            include 'db_connection.php';

            $usuario = $_POST['username'];
            $contraseña = $_POST['password'];

            // Preparar y ejecutar la consulta de manera segura
            $stmt = $conn->prepare("SELECT * FROM login WHERE usuario = ? AND contrasena = ?");
            $stmt->bind_param("ss", $usuario, $contraseña);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Usuario y contraseña correctos, redirigir a dashboard.html
                header("Location: dashboard.html");
                exit();
            } else {
                // Usuario o contraseña incorrectos, mostrar mensaje de error
                echo "<div class='error-message'>Usuario o contraseña incorrectos</div>";
            }

            // Cerrar la conexión
            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</body>
</html>
