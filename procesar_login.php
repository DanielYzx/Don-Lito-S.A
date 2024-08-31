<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta para verificar las credenciales y obtener el nombre
    $sql = "SELECT nombre FROM usuarios WHERE correo = ? AND contraseña = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Iniciar sesión exitosa
        $row = $result->fetch_assoc();
        $_SESSION['user_name'] = $row['nombre']; // Guarda el nombre del usuario en la sesión
        header("Location: index.php"); // Redirige al index
        exit();
    } else {
        // Credenciales incorrectas
        $error = "Usuario o contraseña incorrectos";
        header("Location: index.php?error=" . urlencode($error)); // Redirige al index con el error
        exit();
    }
}
?>