<?php
if (isset($_POST['register'])) {
    include 'db_connection.php';

    $nuevo_usuario = $_POST['new_username'];
    $nueva_contraseña = $_POST['new_password'];

    // Preparar y ejecutar la consulta para insertar el nuevo usuario de manera segura
    $stmt = $conn->prepare("INSERT INTO login (usuario, contrasena) VALUES (?, ?)");
    $stmt->bind_param("ss", $nuevo_usuario, $nueva_contraseña);

    if ($stmt->execute()) {
        // Registro exitoso, redirigir a la página de inicio de sesión o dashboard
        header("Location: login.php");
        exit();
    } else {
        echo "Error: No se pudo registrar el usuario. Por favor, inténtalo de nuevo.";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>
