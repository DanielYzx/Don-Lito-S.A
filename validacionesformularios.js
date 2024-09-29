 // Funciones relacionadas con el formulario de login
 function showLoginForm() {
    document.getElementById('loginFormContainer').style.display = 'flex';
    document.querySelector('input[name="redirect_url"]').value = window.location.href; // Captura la URL actual
}




document.getElementById("closeBtn").addEventListener("click", function() {
    document.getElementById("loginFormContainer").style.display = "none";
    removeErrorParam();
    clearLoginFormFields();

    // Ocultar el mensaje de error
    const errorMsg = document.getElementById("errorMessage");
    if (errorMsg) {
        errorMsg.style.display = "none";

    }

});

function clearLoginFormFields() {
    document.getElementById("loginForm").reset();
}

function removeErrorParam() {
    const url = new URL(window.location);
    url.searchParams.delete('error');
    window.history.replaceState({}, document.title, url);
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        showLoginForm();
        removeErrorParam();
    } else {
        document.getElementById("loginFormContainer").style.display = "none";
    }
});

// Funciones relacionadas con el formulario de registro
function showRegisterForm() {
    document.getElementById('registerFormContainer').style.display = 'flex';
    document.querySelector('input[name="redirect_url"]').value = window.location.href; // Captura la URL actual
}


document.getElementById("closeRegisterBtn").addEventListener("click", function() {
    document.getElementById("registerFormContainer").style.display = "none";
    removeRegisterErrorParam();
    // Ocultar el mensaje de error
    const errorRegisterMsg = document.getElementById("errorRegisterMessage");
    if (errorRegisterMsg) {
        errorRegisterMsg.style.display = "none";
    }

    clearRegisterFormFields();
});

function clearRegisterFormFields() {
    document.getElementById("registerForm").reset();
}

function removeRegisterErrorParam() {
    const url = new URL(window.location);
    url.searchParams.delete('error_register');
    window.history.replaceState({}, document.title, url);
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error_register')) {
        showRegisterForm();
        removeRegisterErrorParam();
    } else {
        document.getElementById("registerFormContainer").style.display = "none";
    }
});


///////////////////////////////////////////////////////////////////////////////////////

// Función para mostrar el formulario de registro y cerrar el de login si está abierto
function showRegisterForm() {
    // Mostrar el formulario de registro
    document.getElementById('registerFormContainer').style.display = 'flex';

    // Cerrar el formulario de inicio de sesión si está abierto
    const loginFormContainer = document.getElementById('loginFormContainer');
    if (loginFormContainer.style.display === 'flex') {
        loginFormContainer.style.display = 'none';
        clearFormFields(); // Limpiar los campos del formulario de login
        removeErrorParam(); // Limpiar el parámetro 'error' si existe
    }
}

// Función para mostrar el formulario de inicio de sesión y cerrar el de registro si está abierto
function showLoginForm() {
    // Mostrar el formulario de inicio de sesión
    document.getElementById('loginFormContainer').style.display = 'flex';

    // Cerrar el formulario de registro si está abierto
    const registerFormContainer = document.getElementById('registerFormContainer');
    if (registerFormContainer.style.display === 'flex') {
        registerFormContainer.style.display = 'none';
        clearRegisterFormFields(); // Limpiar los campos del formulario de registro
        removeRegisterErrorParam(); // Limpiar el parámetro 'error_register' si existe
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////
// aqui inician la Función para  el formulario de restablecimiento de contraseña
// Funciones relacionadas con el formulario de restablecimiento de contraseña
function showResetForm() {
    document.getElementById('resetFormContainer').style.display = 'flex';

    // Solo cierra el formulario de login si está abierto
    closeLoginForm();

    // Aquí podrías manejar qué formulario mostrar, dependiendo del estado actual
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('show_validation_code')) {
        document.getElementById("resetForm").style.display = "none";
        document.getElementById("validationForm").style.display = "block";
    } else if (urlParams.has('show_new_password')) {
        document.getElementById("validationForm").style.display = "none";
        document.getElementById("newPasswordForm").style.display = "block";
    } else {
        document.getElementById("resetForm").style.display = "block";
    }
}

document.getElementById("closeResetBtn").addEventListener("click", function() {
    document.getElementById("resetFormContainer").style.display = "none";
    removeResetErrorParam();
    removeResetSuccessParam();
    clearResetFormFields();

    // Ocultar el mensaje de error
    const errorMsg = document.getElementById("errorResetMessage");
    if (errorMsg) {
        errorMsg.style.display = "none";

    }


    window.location.href = 'index.php?show_reset_form=true';

});

function clearResetFormFields() {
    document.getElementById("resetForm").reset();
}

function removeResetErrorParam() {
    const url = new URL(window.location);
    url.searchParams.delete('error_reset');
    window.history.replaceState({}, document.title, url);
}


document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error_reset')) {
        showResetForm();
        removeResetErrorParam();
    } else if (urlParams.has('success_reset')) {
        showResetForm();
        removeResetSuccessParam();
    } else {
        document.getElementById("resetFormContainer").style.display = "none";
    }
});

// Función para cerrar el formulario de inicio de sesión
function closeLoginForm() {
    document.getElementById("loginFormContainer").style.display = "none";
}

// Función para cerrar el formulario de restablecimiento de contraseña
function closeResetForm() {
    document.getElementById("resetFormContainer").style.display = "none";

}

////////////////////////////////////////////////////////////////////////////
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);

    // Mostrar el formulario de validación de código si se envió el correo correctamente
    if (urlParams.has('show_validation_code')) {
        // Mostrar el contenedor del formulario de restablecimiento
        document.getElementById("resetFormContainer").style.display = "flex";
        // Ocultar el formulario de ingreso de correo
        document.getElementById("resetForm").style.display = "none";
        // Mostrar el formulario de validación de código
        document.getElementById("validationForm").style.display = "block";
    }

    // Mostrar el formulario de nueva contraseña si el código fue validado correctamente
    if (urlParams.has('show_new_password')) {
        document.getElementById("resetFormContainer").style.display = "flex";
        document.getElementById("validationForm").style.display = "none";
        document.getElementById("newPasswordForm").style.display = "block";
    }

    // Manejo de mensajes de error o éxito
    if (urlParams.has('error_reset')) {
        document.getElementById("resetFormContainer").style.display = "flex";
        // Aquí podrías hacer que el mensaje de error sea visible, si no lo es.
    }
});
