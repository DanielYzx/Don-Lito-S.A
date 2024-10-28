<?php
session_start();
include 'conexion.php'; // Asegúrate de incluir la conexión a la base de datos

// Verifica si hay productos en el carrito
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Contador para el total
    $total = 0;
    // Contador para productos únicos
    $contador_productos = count($_SESSION['carrito']); // Contar productos únicos en el carrito
    
    echo '<div class="carrito-container">';
    
    // Botón "Seguir Comprando"
    echo '<div style="text-align: right; margin-bottom: 20px;">';
    echo '<button class="btn-seguir" onclick="window.location.href=\'' . ($_SESSION['pagina_anterior'] ?? 'productos.php') . '\'">Seguir Comprando</button>'; // URL anterior o 'productos.php' como default
    echo '</div>';
    
    echo '<h1>Tu Carrito de Compras</h1>';
    echo '<h2 class="text-left">' . $contador_productos . ' artículo(s) agregado(s)</h2>'; // Mostrar el contador de artículos
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
        // Consulta para obtener el nombre y stock máximo del producto desde la base de datos
        $sql = "SELECT nombre, cantidad_disponible FROM productos WHERE id = ?";
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param('i', $producto_id);
            $stmt->execute();
            $stmt->bind_result($nombre, $stock_maximo);
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
                    <input type="number" value="' . $detalle['cantidad'] . '" min="1" max="' . $stock_maximo . '" class="cantidad-input" data-producto-id="' . $producto_id . '" onchange="mostrarBotonActualizar(' . $producto_id . ')">
                    
                    <button class="btn-cancelar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="cancelarCambio(' . $producto_id . ', ' . $detalle['cantidad'] .')">
                        <img src="img/cancelarcarrito.png" alt="Cancelar" style="width: 15px; height: 15px;">
                    </button>
                    <button class="btn-actualizar" data-producto-id="' . $producto_id . '" style="display:none;" onclick="actualizarCantidad(' . $producto_id . ', ' . $stock_maximo . ')">
                        <img src="img/actualizarcarrito.png" alt="Actualizar" style="width: 15px; height: 15px;">
                    </button>
                </div>';
        echo '</td>';
        echo '<td>$' . number_format($detalle['precio'], 2) . '</td>';
        echo '<td>$' . number_format($subtotal, 2) . '</td>';
        echo '<td>
                <button class="btn-eliminar" onclick="eliminarProducto(' . $producto_id . ')">Eliminar</button>
              </td>';
        echo '</tr>';
    }

    // Calcular el total sin IVA
    $total_sin_iva = $total / 1.13; // Divide el total entre 1.13 para obtener el subtotal
    // Calcular el IVA
    $iva = $total_sin_iva * 0.13; // Calcula el 13% del subtotal
    // Calcular el total con IVA (que sigue siendo el mismo total)
    $total_con_iva = $total; // Este ya es el total que incluye IVA

    echo '</table>';
    echo '<h2 class="text-left subtotal">Subtotal: $' . number_format($total_sin_iva, 2) . '</h2>'; // Total sin IVA
    echo '<h2 class="text-left iva">IVA (13%): $' . number_format($iva, 2) . '</h2>'; // Mostrar el IVA
    echo '<h2 class="text-left total">Total a pagar: $' . number_format($total_con_iva, 2) . '</h2>'; // Total con IVA
    echo '<div class="text-left">'; // Div para alinear el botón
    echo '<button class="btn-finalizar" onclick="enviar()">Enviar Pedido</button>'; // Botón para continuar comprando
    echo '</div>'; // Cerrar div

    echo '</div>'; // Cerrar contenedor de carrito

} else {
    echo '<p>No hay productos en el carrito.</p>';
}
?>
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
function actualizarCantidad(productoId, stockMaximo) {
    const inputCantidad = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
    let nuevaCantidad = parseInt(inputCantidad.value);

    if (nuevaCantidad > stockMaximo) {
        alert(`La cantidad ingresada supera el stock disponible. Solo hay ${stockMaximo} unidades disponibles.`);
        nuevaCantidad = stockMaximo; // Ajusta la cantidad visualmente en el campo de entrada
        inputCantidad.value = stockMaximo; // Cambia la visualización
    }

    if (nuevaCantidad > 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'agregar_al_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    location.reload(); // Recargar la página para ver los cambios
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
function enviar() {
    // Recoge los productos y cantidades del carrito
    const productos = {};
    document.querySelectorAll('.cantidad-input').forEach(input => {
        const productoId = input.dataset.productoId;
        const cantidad = input.value;
        productos[productoId] = cantidad; // Guardar la cantidad por producto
    });

    // Convierte el objeto en una cadena de consulta
    const queryString = Object.entries(productos)
        .map(([id, cantidad]) => `producto_id[]=${id}&cantidad[]=${cantidad}`)
        .join('&');

    // Redirige a la página de procesamiento del pedido
    window.location.href = `procesar_pedido.php?${queryString}`;
}

</script>



<style>
body {
    background-color: #D9D9D9;
    margin: 0; /* Ajuste de márgenes */
    padding: 20px;
    font-family: Arial, sans-serif;
}

.carrito-container {
    max-width: 900px;
    margin: auto;
    padding: 30px;
    background-color: #ffffff; /* Fondo blanco */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Sombra */
    border-radius: 5px; /* Bordes redondeados */
    text-align: center;
}

.text-left {
    text-align: left;
}

table {
    width: 100%;
    border: 1px solid #ddd;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 15px; /* Mayor espacio interno */
    text-align: center; /* Centrar el texto */
}

th {
    background-color: #ADD8E6; /* Celeste claro */
    color: #333; /* Color del texto */
}

tr:nth-child(even) {
    background-color: #f9f9f9; /* Color de fondo alterno */
}

tr:hover {
    background-color: #f1f1f1; /* Color de fondo al pasar el ratón */
}

button {
    padding: 10px 15px; /* Aumentar tamaño del botón */
    margin: 5px;
    cursor: pointer;
    border: none; /* Sin borde */
    border-radius: 5px; /* Bordes redondeados */
    transition: background-color 0.3s, transform 0.2s; /* Transiciones suaves */
    font-size: 1em; /* Tamaño de fuente */
}

.btn-eliminar {
    background-color: #dc3545;
    color: white;
}

.btn-eliminar:hover {
    background-color: #c82333; /* Color más oscuro al pasar el ratón */
    transform: scale(1.05); /* Efecto de aumento al pasar el ratón */
}

.btn-actualizar {
    background-color: #4CAF50;
    color: white;
}

.btn-actualizar:hover {
    background-color: #45a049; /* Color más oscuro al pasar el ratón */
    transform: scale(1.05); /* Efecto de aumento al pasar el ratón */
}

.btn-cancelar {
    background-color: #f0ad4e;
    color: white;
}

.btn-cancelar:hover {
    background-color: #ec971f; /* Color más oscuro al pasar el ratón */
    transform: scale(1.05); /* Efecto de aumento al pasar el ratón */
}

.btn-seguir {
    background-color: #007bff; /* Azul para el botón "Seguir Comprando" */
    color: white;
}

.btn-seguir:hover {
    background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
}

.btn-finalizar {
    background-color: #28a745; /* Verde para el botón "Finalizar Compra" */
    color: white;
}

.btn-finalizar:hover {
    background-color: #218838; /* Verde más oscuro al pasar el ratón */
}

.cantidad-container {
    display: flex;
    align-items: center;
    justify-content: flex-end; /* Alinea los elementos hacia la derecha */
}

.cantidad-input {
    width: 60px;
    margin-right: 5px; /* Margen derecho para separación */
    margin-left: -10px; /* Ajusta el margen izquierdo para moverlo a la izquierda */
    border: 2px solid #4CAF50; /* Borde verde */
    border-radius: 10px; /* Bordes más redondeados */
    padding: 5px; 
    text-align: center; 
    transition: border-color 0.3s; /* Transición de color de borde */
}

.cantidad-input:focus {
    border-color: #3e8e41; /* Color de borde al enfocar */
    outline: none; /* Eliminar el contorno predeterminado */
}

/* Eliminar botones de incremento y decremento en navegadores Webkit (Chrome, Safari, Edge) */
.cantidad-input::-webkit-outer-spin-button,
.cantidad-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Eliminar botones de incremento y decremento en Firefox */
.cantidad-input[type="number"] {
    -moz-appearance: textfield;
}

h2 {
    font-size: 1.2em; 
    margin-bottom: 20px; 
    padding: 10px; /* Espacio interno */
    border-radius: 5px; /* Bordes redondeados */
    font-weight: bold; /* Texto en negrita */
}

.subtotal, .iva {
    font-size: 16px; /* Tamaño de fuente más pequeño */
    margin: 5px 0; /* Espaciado superior e inferior más pequeño */
    color: #555; /* Color más suave para el subtotal y el IVA */
}

.total {
    font-size: 20px; /* Tamaño de fuente más grande para el total */
    font-weight: bold; /* Negrita para destacar el total */
    margin: 10px 0; /* Espaciado mayor para resaltar el total */
    color: #000; /* Color negro para mayor énfasis */
}

</style>



<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>








