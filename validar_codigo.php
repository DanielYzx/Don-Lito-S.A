<?php
// validar_codigo.php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $conexion->real_escape_string($_POST['validation_code']);
    $sql = "SELECT id FROM usuarios WHERE reset_token = '$token' AND token_expiry > NOW()";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        // Código válido, muestra el formulario para cambiar la contraseña
        header("Location: olvido_contrasena.php?token=$token&show_new_password=1");
        exit();
    } else {
        header("Location: olvido_contrasena.php?error_reset=Código inválido o expirado.");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

$conexion->close();
?>