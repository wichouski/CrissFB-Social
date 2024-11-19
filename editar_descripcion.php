<?php
session_start();
include 'db_connection.php';

// Verificar sesión
if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

// Obtener los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $descripcion = $conn->real_escape_string($_POST['descripcion']);

    // Asegurar que el usuario solo pueda editar su propia descripción
    if ($id == $_SESSION['ID']) {
        $query = "UPDATE usuarios SET descripcion = '$descripcion' WHERE id = $id";
        if ($conn->query($query)) {
            header("Location: pestañas_usuarios.php?success=1");
            exit();
        } else {
            echo "Error al actualizar la descripción: " . $conn->error;
        }
    } else {
        echo "No tienes permiso para editar esta descripción.";
    }
}
?>
