<?php
session_start();
include 'db_connection.php';

// Verificar sesión
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['ID'];

// Obtener el nombre y la imagen del usuario que inició sesión
$query = "SELECT usuario, imagen FROM login WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_name = "Usuario"; // Valor predeterminado por si no se encuentra el usuario
$user_image = "user_placeholder.jpg"; // Imagen predeterminada

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_name = $row['usuario']; // Nombre del usuario autenticado
    if (!empty($row['imagen'])) {
        $user_image = $row['imagen']; // Imagen del usuario desde la base de datos
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Publicaciones</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #3a77fc;
            color: white;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: black;
            padding: 20px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .navbar .left-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar .avatar-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            background-color: gray;
            border: none;
            cursor: pointer;
            padding: 0;
        }
        .navbar .avatar-button img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .navbar img.logo {
            flex-grow: 0;
            display: block;
            margin: -20px;
        }
        .navbar .right-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .navbar .button-image {
            width: 80px;
            height: 110px;
            margin: -20px;
            cursor: pointer;
        }
        .section-header {
            margin-top: 120px;
            padding: 20px;
            background-color: #343434;
            text-align: center;
            font-size: 1.8rem;
            color: white;
        }
        .main-content {
            padding: 20px;
        }
        .post-list-container {
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 50px;
        }
        .post-item {
            background-color: #343434;
            color: white;
            padding: 20px;
            margin-bottom: 10px;
            border-radius: 30px;
            display: flex;
            flex-direction: column;
        }
        .post-item .info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .post-item .info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .post-item button {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .post-item button:hover {
            background-color: white;
            color: black;
        }
    </style>
</head>
<body>
<div class="navbar">
    <!-- Sección izquierda con el ícono de usuario -->
    <div class="left-section">
        <form action="usuario.php" method="GET" style="margin: 0;">
            <button class="avatar-button" type="submit">
                <img src="resources<?php echo htmlspecialchars($user_image); ?>" alt="Perfil de <?php echo htmlspecialchars($user_name); ?>">
            </button>
        </form>
        <span><?php echo htmlspecialchars($user_name); ?></span> <!-- Mostrar el nombre del usuario -->
    </div>

    <!-- Imagen del logo centrada -->
    <img src="resources/logo con letra.gif" alt="Animación GIF" width="250" height="130" class="logo">

    <!-- Botones de la derecha con imágenes -->
    <div class="right-buttons">
        <a href="crear_publicacion.php">
            <img src="resources/4.gif" alt="Crear Publicación" class="button-image">
        </a>
        <a href="login.php">
            <img src="resources/3.gif" alt="Cerrar Sesión" class="button-image">
        </a>
    </div>
</div>

<div class="section-header">
    FBPosts
</div>

<div class="main-content">
    <div class="post-list-container">
        <div class="post-list">
            <?php
            $query = "SELECT p.id_p, p.ID, p.contenido, p.fecha, l.usuario, l.imagen 
                      FROM publicaciones p 
                      JOIN login l ON p.ID = l.id 
                      ORDER BY p.fecha DESC";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                $profile_image = !empty($row['imagen']) ? $row['imagen'] : 'user_placeholder.jpg'; // Imagen predeterminada si no hay imagen
                echo "<div class='post-item'>";
                echo "<div class='info'>";
                echo "<img src='resources/" . htmlspecialchars($profile_image) . "' alt='Foto de perfil'>";
                echo "<p class='usuario'>" . htmlspecialchars($row['usuario']) . "</p>";
                echo "</div>";
                echo "<p style='margin-top: 10px;'>" . htmlspecialchars($row['contenido']) . "</p>";
                echo "<p><small>" . $row['fecha'] . "</small></p>";
                if ($row['ID'] == $user_id) {
                    echo "<button onclick='editarPublicacion(" . $row['id_p'] . ")'>Editar</button>";
                    echo "<button onclick='eliminarPublicacion(" . $row['id_p'] . ")'>Eliminar</button>";
                }
                echo "</div>";
            }
            $conn->close();
            ?>
        </div>
    </div>
</div>

<script>
function eliminarPublicacion(id_p) {
    if (confirm("¿Estás seguro de que deseas eliminar esta publicación?")) {
        fetch('eliminar_publicacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_p=${id_p}`
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'Publicación eliminada') {
                location.reload();
            } else {
                alert('Error al eliminar la publicación');
            }
        });
    }
}

function editarPublicacion(id_p) {
    const nuevoContenido = prompt("Escribe el nuevo contenido de la publicación:");
    if (nuevoContenido) {
        fetch('editar_publicacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_p=${id_p}&contenido=${encodeURIComponent(nuevoContenido)}`
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'Publicación actualizada') {
                location.reload();
            } else {
                alert('Error al actualizar la publicación');
            }
        });
    }
}
</script>
</body>
</html>
