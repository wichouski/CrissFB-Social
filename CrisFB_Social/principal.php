<?php
session_start();
include 'db_connection.php';

// Verificar sesión
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['ID'];
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
        .navbar img.avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: gray;
        }
        .navbar img.logo {
            flex-grow: 0;
            display: block;
            margin: -20px;
        }
        .navbar .right-buttons {
            display: flex;
            gap: 10px; /* Ajustamos el espacio entre los botones */
            align-items: center;
        }
        .navbar .button-image {
            width: 80px;
            height: 110px;
            margin: -20px;
            cursor: pointer;
        }
        .section-header {
            margin-top: 100px;
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
            border-radius: 5px;
        }
        .post-item {
            background-color: white;
            color: black;
            padding: 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        <img src="user_placeholder.jpg" alt="Usuario" class="avatar">
        <span>Usuario</span>
    </div>

    <!-- Imagen del logo centrada -->
    <img src="resources/logo con letra.gif" alt="Animación GIF" width="200" height="100" class="logo">

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
            $query = "SELECT p.id_p, p.ID, p.contenido, p.fecha, l.usuario 
                      FROM publicaciones p 
                      JOIN login l ON p.ID = l.id 
                      ORDER BY p.fecha DESC";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                echo "<div class='post-item'>";
                echo "<div class='info'>";
                echo "<p class='usuario'>" . htmlspecialchars($row['usuario']) . "</p>";
                echo "<p>" . htmlspecialchars($row['contenido']) . "</p>";
                echo "<p><small>" . $row['fecha'] . "</small></p>";
                echo "</div>";
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
