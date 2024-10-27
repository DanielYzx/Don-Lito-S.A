// Inicializar la cantidad guardada
let cantidadAnterior = JSON.parse(localStorage.getItem('cantidadAnterior')) || {};

// Función para guardar en localStorage
function guardarCantidadEnLocalStorage(productoId, cantidad) {
    cantidadAnterior[productoId] = cantidad;
    localStorage.setItem('cantidadAnterior', JSON.stringify(cantidadAnterior));
}

// Función para guardar el estado del botón en localStorage
function guardarEstadoBotonEnLocalStorage(productoId, estado) {
    localStorage.setItem(`estado_boton_${productoId}`, estado);
}

// Mostrar las cantidades guardadas en los inputs
document.querySelectorAll('.cantidad-input').forEach(function(input) {
    const productoId = input.getAttribute('data-producto-id');

    // Cargar cantidad desde localStorage si existe
    if (cantidadAnterior[productoId]) {
        input.value = cantidadAnterior[productoId];
    }

    input.addEventListener('input', function() {
        // Validar que el valor no sea menor que 1
        if (parseInt(input.value) < 1) {
            input.value = 1; // Establece el valor a 1 si es menor que 1
        }

        guardarCantidadEnLocalStorage(productoId, input.value);

        const opciones = this.parentNode.nextElementSibling;
        opciones.style.display = "flex";

        // Deshabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling.nextElementSibling;
        botonCarrito.disabled = true;
        botonCarrito.classList.add('btn-deshabilitado');
    });
});

// Mostrar los botones de "Cancelar" y "Actualizar" al hacer click en los botones de sumar/restar
document.querySelectorAll('.cantidad-btn').forEach(function(button) {
    button.addEventListener('click', function() {
        const input = this.parentNode.querySelector('.cantidad-input');
        let value = parseInt(input.value);
        const productoId = input.getAttribute('data-producto-id');

        if (!cantidadAnterior[productoId]) {
            cantidadAnterior[productoId] = value;
        }

        const opciones = this.parentNode.nextElementSibling;
        opciones.style.display = "flex";

        // Deshabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling.nextElementSibling;
        botonCarrito.disabled = true;
        botonCarrito.classList.add('btn-deshabilitado');

        if (this.classList.contains('restar')) {
            if (value > 1) {
                input.value = value - 1;
            }
        } else if (this.classList.contains('sumar')) {
            input.value = value + 1;
        }

        // Guardar en localStorage
        guardarCantidadEnLocalStorage(productoId, input.value);
    });
});

// Funcionalidad para Cancelar o Actualizar la cantidad
document.querySelectorAll('.btn-cancelar-cambio').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = this.parentNode.previousElementSibling.querySelector('.cantidad-input');
        const productoId = input.getAttribute('data-producto-id'); 

        input.value = cantidadAnterior[productoId] || 1; 
        this.parentNode.style.display = 'none';

        // Rehabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling;
        botonCarrito.disabled = false;
        botonCarrito.classList.remove('btn-deshabilitado');
    });
});

document.querySelectorAll('.btn-actualizar-cambio').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = this.parentNode.previousElementSibling.querySelector('.cantidad-input');
        const cantidad = parseInt(input.value);
        const cantidadDisponible = parseInt(input.getAttribute('data-disponible')); 
        const productoId = input.getAttribute('data-producto-id'); 

        // Validar si la cantidad ingresada es mayor que la disponible
        if (cantidad > cantidadDisponible) {
            alert(`Lo sentimos no se puede actualizar la cantidad porque no hay suficientes productos en existencia. Solo hay ${cantidadDisponible} disponibles.`);
            input.value = cantidadDisponible; // Actualiza el input a la cantidad disponible
            return; // Salir de la función
        }

        // Guardar en localStorage
        guardarCantidadEnLocalStorage(productoId, cantidad);
        // Aquí puedes añadir lógica adicional para actualizar el carrito en el servidor
        // Ejemplo de actualización en el servidor (puedes modificar esto según tus necesidades)
        actualizarCarritoEnServidor(productoId, cantidad);
        
        // Ocultar opciones después de actualizar
        this.parentNode.style.display = 'none';
        
        // Rehabilitar el botón de agregar/eliminar
        const botonCarrito = this.parentNode.nextElementSibling;
        botonCarrito.disabled = false;
        botonCarrito.classList.remove('btn-deshabilitado');
    });
});

// Función para actualizar el carrito en el servidor (puedes adaptarlo según tu backend)
function actualizarCarritoEnServidor(productoId, cantidad) {
    // Implementa aquí la lógica para enviar la actualización al servidor
    console.log(`Actualizando producto ${productoId} a la cantidad ${cantidad}`);
}

// Cargar el estado del botón al inicializar la página
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los botones de añadir al carrito
    const buttons = document.querySelectorAll('.agregar-carrito-btn'); // Asegúrate de que esta clase sea correcta

    buttons.forEach(function(button) {
        const productId = button.getAttribute('data-producto-id');
        const estadoBoton = localStorage.getItem(`estado_boton_${productId}`);

        if (estadoBoton === 'added') {
            const img = button.querySelector('img');
            img.src = 'img/eliminarcarrito.png';
            img.alt = 'Eliminar del carrito';
            button.style.backgroundColor = '#dc3545';
        } else if (estadoBoton === 'removed') {
            const img = button.querySelector('img');
            img.src = 'img/agregarcarrito.png';
            img.alt = 'Añadir al carrito';
            button.style.backgroundColor = '#28a745';
        }
    });
});










