<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['ID'])) {
        $user_id = $_SESSION['ID'];
        $id_p = $_POST['id_p'];

        // Eliminar la publicación si pertenece al usuario
        $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id_p = ? AND ID = ?");
        $stmt->bind_param("ii", $id_p, $user_id);

        if ($stmt->execute()) {
            echo "Publicación eliminada";
        } else {
            echo "Error al eliminar la publicación: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "No autorizado";
    }

    $conn->close();
}
?>
