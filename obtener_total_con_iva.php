<?php
session_start();

// Definir el formato de la respuesta
$response = ['success' => false, 'totalConIVA' => 0];

if (isset($_SESSION['total_con_iva'])) {
    // Si el total con IVA está disponible, devuelvelo
    $response['success'] = true;
    $response['totalConIVA'] = $_SESSION['total_con_iva'];
} else {
    // Si no está disponible, se devuelve un error
    $response['error'] = 'No se encontró el total con IVA en la sesión.';
}

// Devolver el resultado en formato JSON
echo json_encode($response);
?>
