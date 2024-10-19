<?php
session_start();
include 'conexion.php'; // Asegúrate de incluir la conexión a la base de datos si la necesitas

// Verifica si hay productos en el carrito
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Contador para el total
    $total = 0;

    echo '<div class="carrito-container">';
    echo '<h1>Tu Carrito de Compras</h1>';
    echo '<table>';
    echo '<tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Acciones</th>
          </tr>';

    // Recorrer los productos en el carrito
    foreach ($_SESSION['carrito'] as $producto_id => $detalle) {
        // Consulta para obtener el nombre del producto desde la base de datos
        $sql = "SELECT nombre FROM productos WHERE id = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('i', $producto_id);
            $stmt->execute();
            $stmt->bind_result($nombre);
            $stmt->fetch();
            $stmt->close();
        }

        // Calcular el total del producto
        $subtotal = $detalle['cantidad'] * $detalle['precio'];
        $total += $subtotal;

        // Mostrar producto en la tabla
        echo '<tr>';
        echo '<td>' . htmlspecialchars($nombre) . '</td>';
        echo '<td>
                <div class="cantidad-container">
                    <input type="number" value="' . $detalle['cantidad'] . '" min="1" class="cantidad-input" data-producto-id="' . $producto_id . '" onchange="mostrarBotonActualizar(' . $producto_id . ')">
                    
                    <button class="btn-cancelar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="cancelarCambio(' . $producto_id . ', ' . $detalle['cantidad'] .')">
                        <img src="img/cancelarcarrito.png" alt="Actualizar" style="width: 15px; height: 15px;">
                    </button>
                    <button class="btn-actualizar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="actualizarCantidad(' . $producto_id . ')">
                        <img src="img/actualizarcarrito.png" alt="Actualizar" style="width: 15px; height: 15px;">
                    </button>
                </div>';
        echo '<td>$' . number_format($detalle['precio'], 2) . '</td>';
        echo '<td>$' . number_format($subtotal, 2) . '</td>';
        echo '<td>
                <button class="btn-eliminar" onclick="eliminarProducto(' . $producto_id . ')">Eliminar</button>
              </td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<h2>Total: $' . number_format($total, 2) . '</h2>';
    echo '<button onclick="irAComprar()">Finalizar Compra</button>'; // O un botón para continuar comprando

    echo '</div>'; // Cerrar contenedor de carrito

} else {
    echo '<p>No hay productos en el carrito.</p>';
}
?>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
function eliminarProducto(productoId) {
    if (confirm("¿Estás seguro de que quieres eliminar este producto del carrito?")) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'agregar_al_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error al eliminar el producto: ' + response.error);
                }
            }
        };
        xhr.send(`producto_id=${productoId}&action=remove`);
    }
}

function mostrarBotonActualizar(productoId) {
    const botonActualizar = document.querySelector(`.btn-actualizar[data-producto-id="${productoId}"]`);
    const botonCancelar = document.querySelector(`.btn-cancelar[data-producto-id="${productoId}"]`);
    botonActualizar.style.display = 'inline-block'; // Mostrar el botón de actualización
    botonCancelar.style.display = 'inline-block'; // Mostrar el botón de cancelar
}

function cancelarCambio(productoId, cantidadAnterior) {
    const inputCantidad = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
    inputCantidad.value = cantidadAnterior; // Restablece el valor al anterior
    ocultarBotones(productoId); // Ocultar los botones de actualizar y cancelar
}

function ocultarBotones(productoId) {
    const botonActualizar = document.querySelector(`.btn-actualizar[data-producto-id="${productoId}"]`);
    const botonCancelar = document.querySelector(`.btn-cancelar[data-producto-id="${productoId}"]`);
    botonActualizar.style.display = 'none';
    botonCancelar.style.display = 'none';
}

function actualizarCantidad(productoId) {
    const inputCantidad = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
    const nuevaCantidad = parseInt(inputCantidad.value);

    if (nuevaCantidad > 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'agregar_al_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error al actualizar la cantidad: ' + response.error);
                }
            }
        };
        xhr.send(`producto_id=${productoId}&action=update&cantidad=${nuevaCantidad}`);
    } else {
        alert("La cantidad debe ser mayor que cero.");
    }
}

function irAComprar() {
    window.location.href = 'pagina_pago.php'; // Cambia esto según la ruta de tu página de pago
}
</script>

<style>
.carrito-container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f4f4f4;
}

button {
    padding: 5px 10px;
    margin: 5px;
    cursor: pointer;
}

.btn-eliminar {
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 10px;
}

.cantidad-container {
    display: flex;
    align-items: center;
}

.cantidad-input {
    width: 60px;
    margin-right: 10px; /* Espacio entre el input y el botón */
}

.btn-actualizar {
    background-color: #4CAF50; /* Color de fondo */
    color: white; /* Color del texto */
    border: none; /* Sin borde */
    border-radius: 25px; /* Bordes redondeados */
    padding: 5px 15px; /* Espaciado interno */
    cursor: pointer; /* Cambia el cursor al pasar el ratón */
    display: flex; /* Para centrar la imagen dentro del botón */
    align-items: center; /* Alinea verticalmente */
    justify-content: center; /* Alinea horizontalmente */
}

.btn-cancelar {
    background-color: #f0ad4e; /* Color de fondo para cancelar */
    color: white; /* Color del texto */
    border: none; /* Sin borde */
    border-radius: 5px; /* Bordes redondeados */
    padding: 5px 10px; /* Espaciado interno */
    cursor: pointer; /* Cambia el cursor al pasar el ratón */
}
</style>



