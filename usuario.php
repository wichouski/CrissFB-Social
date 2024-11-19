<?php
session_start();
include 'db_connection.php';

// Verificar sesión
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['ID'];

// Inicializar variables para evitar errores
$user = [
    'usuario' => 'Usuario no encontrado',
    'descripcion' => 'Descripción no disponible',
    'imagen' => 'default.jpg'
];

// Obtener información del usuario actual desde la tabla `login`
$query = "SELECT usuario, descripcion, imagen FROM login WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
$stmt->close();

// Manejar la actualización de la descripción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion'])) {
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $update_query = "UPDATE login SET descripcion = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $descripcion, $user_id);
    if ($update_stmt->execute()) {
        $success_message = "Descripción actualizada con éxito.";
        $user['descripcion'] = $descripcion;
    } else {
        $error_message = "Error al actualizar la descripción.";
    }
    $update_stmt->close();
}

// Manejar la selección de imagen de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['imagen'])) {
    $imagen = basename($_POST['imagen']); // Obtener solo el nombre del archivo
    $update_image_query = "UPDATE login SET imagen = ? WHERE id = ?";
    $update_image_stmt = $conn->prepare($update_image_query);
    $update_image_stmt->bind_param("si", $imagen, $user_id);
    if ($update_image_stmt->execute()) {
        $success_message = "Imagen de perfil actualizada con éxito.";
        $user['imagen'] = $imagen;
    } else {
        $error_message = "Error al actualizar la imagen de perfil.";
    }
    $update_image_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <style>
        /* Estilos generales */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            padding: 20px;
            background-color: #f4f4f4;
        }
        .user-profile {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .user-profile img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 20px;
            cursor: pointer;
        }
        .user-profile h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .user-profile p {
            text-align: center;
            font-size: 1rem;
            color: #555;
        }
        .user-profile form {
            margin-top: 20px;
        }
        .user-profile textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .user-profile button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background-color: #3a77fc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .user-profile button:hover {
            background-color: #2f5fc0;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            font-size: 1rem;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
        .image-selection {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            z-index: 1000;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .image-selection img {
            width: 100px;
            height: 100px;
            border-radius: 5px;
            margin: 10px;
            cursor: pointer;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="user-profile">
        <!-- Imagen de perfil -->
        <img src="resources2/<?php echo htmlspecialchars($user['imagen']); ?>" alt="Perfil de <?php echo htmlspecialchars($user['usuario']); ?>" onclick="openImageSelection()">

        <!-- Nombre del usuario -->
        <h2><?php echo htmlspecialchars($user['usuario']); ?></h2>

        <!-- Descripción del usuario -->
        <p><?php echo htmlspecialchars($user['descripcion']); ?></p>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)) : ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)) : ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Formulario para actualizar descripción -->
        <form method="POST">
            <label for="descripcion">Actualizar descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4" required><?php echo htmlspecialchars($user['descripcion']); ?></textarea>
            <button type="submit">Actualizar</button>
        </form>
    </div>

    <!-- Ventana flotante para selección de imagen -->
    <div class="overlay" id="overlay" onclick="closeImageSelection()"></div>
    <div class="image-selection" id="imageSelection">
        <h3>Selecciona una imagen de perfil</h3>
        <form method="POST">
            <?php
            $images = glob(__DIR__ . "/resources2/*.gif");
            foreach ($images as $image) {
                $basename = basename($image);
                $relative_path = "resources2/" . $basename;
                echo "<button name='imagen' value='$basename'><img src='$relative_path' alt='$basename'></button>";
            }
            ?>
        </form>
    </div>

    <script>
        function openImageSelection() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('imageSelection').style.display = 'block';
        }

        function closeImageSelection() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('imageSelection').style.display = 'none';
        }
    </script>
</body>
</html>
