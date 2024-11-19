<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['ID'])) {
        $user_id = $_SESSION['ID'];
        $contenido = $_POST['contenido'];
        $fecha = date('Y-m-d H:i:s'); 

       
        $stmt = $conn->prepare("INSERT INTO publicaciones (ID, contenido, fecha) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }
        
        $stmt->bind_param("iss", $user_id, $contenido, $fecha);

        if ($stmt->execute()) {
            echo "<p>Publicación guardada</p>";
        } else {
            echo "<p>Error al guardar la publicación: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>No autorizado</p>";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Social - Crear Publicación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #222222;
            margin: 0;
            padding: 0;
            color: white;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        header {
            background-color: black;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        .post-form {
            background-color:  #76756e;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .post-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
            background-color: white;
        }
        .post-form button {
            background-color: #3b5998;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>CrissFB Social</h1>
    </header>

    <div class="container">
        <div class="post-form">
            <h2>Crear una publicación</h2>
            <form method="POST" action="">
                <textarea name="contenido" placeholder="¿Qué estás pensando?" required></textarea>
                <button type="submit">Publicar</button>
                <button type="button" onclick="confirmRedirect()">Volver a inicio</button>
                <script>
                    function confirmRedirect() {
                        const confirmar = confirm("¿Seguro que quieres continuar?");
                        if (confirmar) {
                            window.location.href = 'principal.php';
                        }
                    }
                </script>
            </form>
        </div>
    </div>
</body>
</html>