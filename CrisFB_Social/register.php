<?php
if (isset($_POST['register'])) {
    include 'db_connection.php';

    $nuevo_usuario = $_POST['new_username'];
    $nueva_contraseña = $_POST['new_password'];

    
    $stmt = $conn->prepare("INSERT INTO login (usuario, contrasena) VALUES (?, ?)");
    $stmt->bind_param("ss", $nuevo_usuario, $nueva_contraseña);

    if ($stmt->execute()) {
        
        header("Location: login.php");
        exit();
    } else {
        echo "Error: No se pudo registrar el usuario. Por favor, inténtalo de nuevo.";
    }

    
    $stmt->close();
    $conn->close();
}
?>
