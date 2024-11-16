<?php
// Inicia la sesión
session_start();
// Incluir archivo de conexión
include 'conexion.php';

// Verificar si los datos del formulario fueron enviados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar si las contraseñas coinciden
    if ($password != $confirm_password) {
        $_SESSION['form_data'] = $_POST;  // Guardar los datos del formulario para repoblación
        $_SESSION['error_register'] = 'Las contraseñas no coinciden'; // Guardar el error
        header("Location: index.php?error_register=" . urlencode($_SESSION['error_register'])); // Redirigir de vuelta al formulario de registro
        exit();
    }

    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT id FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['form_data'] = $_POST;  // Guardar los datos del formulario para repoblación
        $_SESSION['error_register'] = 'El correo electrónico ya está registrado';
        header("Location: index.php?error_register=" . urlencode($_SESSION['error_register']));
        exit();
    }

    $stmt->close();

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, dirección) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password_hash, $direccion);
    if ($stmt->execute()) {
        // Registro exitoso, redirigir a la página principal o a una página de bienvenida
        $_SESSION['user_id'] = $stmt->insert_id; 
        $_SESSION['user_name'] = $name;
        unset($_SESSION['form_data']); // Limpiar los datos del formulario
        unset($_SESSION['error_register']); // Limpiar el error
        header("Location: index.php");
    } else {
        // Error al registrar
        $_SESSION['error_register'] = 'Hubo un error al crear la cuenta';
        header("Location: index.php");
    }

    $stmt->close();
    $conexion->close();
}
?>                     
