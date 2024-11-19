<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['ID'])) {
        $user_id = $_SESSION['ID'];
        $id_p = $_POST['id_p'];
        $contenido = $_POST['contenido'];

        // Actualizar la publicación si pertenece al usuario
        $stmt = $conn->prepare("UPDATE publicaciones SET contenido = ? WHERE id_p = ? AND ID = ?");
        $stmt->bind_param("sii", $contenido, $id_p, $user_id);

        if ($stmt->execute()) {
            echo "Publicación actualizada";
        } else {
            echo "Error al actualizar la publicación: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "No autorizado";
    }

    $conn->close();
}
?>
