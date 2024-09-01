<?php
session_start();

// Limpiar las variables de sesión relacionadas con el registro
unset($_SESSION['form_data']);
unset($_SESSION['error_register']);

echo "Session data cleared";
?>