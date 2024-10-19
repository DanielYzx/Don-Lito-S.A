function showCarrito() {
    var carritoContainer = document.getElementById('carritoContainer');
    carritoContainer.style.display = 'block';
    document.body.classList.add('no-scroll'); // Evitar desplazamiento en la página
}

document.getElementById("closeCarritoBtn").addEventListener("click", function() {
    document.getElementById("carritoContainer").style.display = "none";
    document.body.classList.remove('no-scroll'); // Volver a permitir el desplazamiento
});




function mostrarBotonActualizar(productoId) {
    const botonActualizar = document.querySelector(`.btn-actualizar[data-producto-id="${productoId}"]`);
    const botonCancelar = document.querySelector(`.btn-cancelar[data-producto-id="${productoId}"]`);
    botonActualizar.style.display = 'inline-block';
    botonCancelar.style.display = 'inline-block';
}

function cancelarCambio(productoId, cantidadAnterior) {
    const inputCantidad = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
    inputCantidad.value = cantidadAnterior;
    ocultarBotones(productoId);
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
        xhr.open('POST', 'actualizar_carrito.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    alert(response.message);
                    // Actualiza la cantidad en el campo de entrada al máximo disponible
                    inputCantidad.value = response.cantidad_disponible;
                }
                document.querySelector('#carritoContainer .carrito-container').innerHTML = response.html;
                ocultarBotones(productoId); // Ocultar los botones después de la actualización
            } else {
                alert('Error al procesar la solicitud.');
            }
        };
        xhr.send(`producto_id=${productoId}&cantidad=${nuevaCantidad}`);
    } else {
        alert("La cantidad debe ser mayor que cero.");
    }
}






function irAComprar() {
    window.location.href = 'pagina_pago.php'; // Cambia esto según la ruta de tu página de pago
}


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


