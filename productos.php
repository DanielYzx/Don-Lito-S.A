<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo.css">
    <title>Productos</title>
</head>
<body>
<?php
include 'conexion.php';

// Obtener el ID de la categoría desde la URL
$categoria_id = $_GET['categoria_id'];

// Consulta para obtener los productos de la categoría seleccionada
$sql = "SELECT nombre, precio, descripción, imagen FROM productos WHERE categoria_id = $categoria_id";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="productos-container">';
    while ($producto = $result->fetch_assoc()) {
       // Mostrar producto en una tarjeta personalizada
       echo '<div class="card-product">';
       echo '<div class="card-product-img">';
       echo '<img src="img/oferta1.jpg" alt="Imagen por defecto">'; // Puedes cambiar la ruta de la imagen según sea necesario
       echo '</div>';
       echo '<div class="card-product-body">';
       echo '<h2 class="card-product-title">' . $producto["nombre"] . '</h2>';
       echo '<p class="card-product-price">Precio: $' . $producto["precio"] . '</p>';
       echo '<p class="card-product-description">' . $producto["descripción"] . '</p>';
       
       // Sección de cantidad
       echo '<div class="producto-cantidad">';
       echo '<button class="cantidad-btn restar">-</button>';
       echo '<input type="number" value="1" min="1" class="cantidad-input">';
       echo '<button class="cantidad-btn sumar">+</button>';
       echo '</div>';
       
       // Botones de cancelar y actualizar ocultos
       echo '<div class="opciones-cantidad" style="display:none;">';
       echo '<button class="btn-cancelar-cambio">
              <img src="img/cancelarcarrito.png" alt="Cancelar" style="width: 20px; height: 20px;">
             </button>';
       echo '<button class="btn-actualizar-cambio">
             <img src="img/actualizarcarrito.png" alt="Actualizar" style="width: 20px; height: 20px;">
             </button>';
       echo '</div>';
       
       
       // Botón de agregar al carrito o eliminar del carrito
       echo '<button class="agregar-carrito-btn">
              <img src="img/agregarcarrito.png" alt="Añadir al carrito" style="width: 25px; height: 25px;">
              </button>';
       echo '</div>';
       echo '</div>';
   }
   echo '</div>'; // Cerrar el contenedor de productos
} else {
   echo '<p>No hay productos disponibles en esta categoría.</p>';
}

$conexion->close();
?>

<script>
// Inicializar la cantidad guardada
let cantidadAnterior = {};

// Mostrar los botones de "Cancelar" y "Actualizar" al cambiar la cantidad manualmente o con los botones
document.querySelectorAll('.cantidad-input').forEach(function(input) {
    input.addEventListener('input', function() {
        const productoId = input.getAttribute('data-producto-id'); // Asumimos que cada input tiene un ID de producto

        // Si no hay una cantidad anterior registrada, guardarla
        if (!cantidadAnterior[productoId]) {
            cantidadAnterior[productoId] = input.value;
        }

        // Mostrar los botones de opciones
        const opciones = this.parentNode.nextElementSibling;
        opciones.style.display = "flex";
    });
});

// Mostrar los botones de "Cancelar" y "Actualizar" al hacer click en los botones de sumar/restar
document.querySelectorAll('.cantidad-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        const input = this.parentNode.querySelector('.cantidad-input');
        let value = parseInt(input.value);
        const productoId = input.getAttribute('data-producto-id');

        // Si no hay una cantidad anterior registrada, guardarla
        if (!cantidadAnterior[productoId]) {
            cantidadAnterior[productoId] = value;
        }

        // Mostrar los botones de opciones
        const opciones = this.parentNode.nextElementSibling;
        opciones.style.display = "flex";

        if (this.classList.contains('restar')) {
            if (value > 1) {
                input.value = value - 1;
            }
        } else if (this.classList.contains('sumar')) {
            input.value = value + 1;
        }
    });
});

// Funcionalidad para Cancelar o Actualizar la cantidad
document.querySelectorAll('.btn-cancelar-cambio').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = this.parentNode.previousElementSibling.querySelector('.cantidad-input');
        const productoId = input.getAttribute('data-producto-id'); 

        // Restaurar la cantidad anterior
        input.value = cantidadAnterior[productoId] || 1; 
        this.parentNode.style.display = 'none'; // Ocultar los botones de cambio
    });
});

document.querySelectorAll('.btn-actualizar-cambio').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = this.parentNode.previousElementSibling.querySelector('.cantidad-input');
        const productoId = input.getAttribute('data-producto-id'); 

        // Guardar la cantidad actual antes de ocultar los botones
        cantidadAnterior[productoId] = input.value;

        this.parentNode.style.display = 'none'; // Ocultar los botones de cambio
    });
});

// Cambiar el botón "Añadir al carrito" a "Eliminar del carrito" con confirmación
document.querySelectorAll('.agregar-carrito-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        const img = this.querySelector('img');
        
        if (img.src.includes('agregarcarrito.png')) {
            // Cambiar a "Eliminar del carrito"
            img.src = 'img/eliminarcarrito.png'; // Cambia la ruta a la imagen de eliminar del carrito
            img.alt = 'Eliminar del carrito';
            this.style.backgroundColor = '#dc3545'; // Cambiar el color a rojo para "Eliminar"
        } else {
            // Confirmación antes de eliminar del carrito
            const confirmacion = confirm("¿Deseas eliminar este producto del carrito?");
            if (confirmacion) {
                // Cambiar de nuevo a "Añadir al carrito"
                img.src = 'img/agregarcarrito.png'; // Cambia la ruta a la imagen de añadir al carrito
                img.alt = 'Añadir al carrito';
                this.style.backgroundColor = '#28a745'; // Cambiar de nuevo a verde para "Añadir"
            }
        }
    });
});


</script>

</body>
</html>   