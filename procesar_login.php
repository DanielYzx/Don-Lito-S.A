<?php
session_start(); // Iniciar sesión
include 'conexion.php'; // Conectar a la base de datos

// Obtener datos del formulario
$email = $_POST['email'];
$password = $_POST['password'];

// Consulta para verificar las credenciales
$sql = "SELECT id, nombre, password FROM usuarios WHERE correo = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verificar si la contraseña coincide
    if (password_verify($password, $user['password'])) {
        // Credenciales correctas, iniciar sesión
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        header("Location: index.php"); // Redirigir al index
        exit();
    } else {
        // Contraseña incorrecta
        $_SESSION['login_error'] = "Credenciales incorrectas. Por favor, inténtalo de nuevo.";
        header("Location: index.php"); // Redirigir de nuevo a la página principal
        exit();
    }
} else {
    // Usuario no encontrado
    $_SESSION['login_error'] = "Credenciales incorrectas. Por favor, inténtalo de nuevo.";
    header("Location: index.php"); // Redirigir de nuevo a la página principal
    exit();
}

$stmt->close();
$conexion->close();
?>
