<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_publicacion = $_POST['id_publicacion'];
    $contenido = $_POST['contenido'];
    $ID = $_SESSION['ID'];

    $stmt = $conn->prepare("INSERT INTO comentarios (id_publicacion, ID, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_publicacion, $ID, $contenido);

    if ($stmt->execute()) {
        header("Location: principal.php");
    } else {
        echo "Error al agregar el comentario.";
    }
    $stmt->close();
}

$conn->close();
?>
