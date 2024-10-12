

 // Funciones relacionadas con el formulario de login empleado
 function showLoginFormem() {
    document.getElementById('loginemFormContainer').style.display = 'flex';
}

document.getElementById("closeBtnem").addEventListener("click", function() {
    document.getElementById("loginemFormContainer").style.display = "none";
    removeErrorParam();
    clearLoginFormFields();

    // Ocultar el mensaje de error
    const errorMsg = document.getElementById("errorMessage");
    if (errorMsg) {
        errorMsg.style.display = "none";

    }

});



function showLoginForm() {
    var loginFormContainer = document.getElementById('loginFormContainer');
    loginFormContainer.style.display = 'block';  // Mostrar el formulario de login
    document.body.classList.add('no-scroll');    // Evitar que la página se desplace mientras se muestra el formulario
    document.getElementById('loginFormContainer').style.display = 'flex';
    document.querySelector('input[name="redirect_url"]').value = window.location.href; // Captura la URL act
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
// Funciones relacionadas con el formulario de restablecimiento de contraseña
function showResetForm() {
    document.getElementById('resetFormContainer').style.display = 'flex';
    closeLoginForm(); // Solo cierra el formulario de login si está abierto

    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('show_validation_code')) {
        toggleForms('resetForm', 'validationForm');
    } else if (urlParams.has('show_new_password')) {
        toggleForms('validationForm', 'newPasswordForm');
    } else {
        toggleForms('', 'resetForm');
    }
}

// Función para mostrar un formulario y ocultar otro
function toggleForms(hideForm, showForm) {
    if (hideForm) document.getElementById(hideForm).style.display = "none";
    if (showForm) document.getElementById(showForm).style.display = "block";
}

// Función para limpiar parámetros relacionados con el restablecimiento
function clearResetParams() {
    const url = new URL(window.location);
    ['show_validation_code', 'show_new_password', 'error_reset', 'success_password_change'].forEach(param => {
        url.searchParams.delete(param);
    });
    window.history.replaceState({}, document.title, url);
}

document.getElementById("closeResetBtn").addEventListener("click", function() {
    document.getElementById("resetFormContainer").style.display = "none";
    clearResetParams();
    clearResetFormFields();

    const errorMsg = document.getElementById("errorResetMessage");
    if (errorMsg) {
        errorMsg.style.display = "none";
    }

    window.location.href = 'index.php?show_reset_form=true';
});

// Limpiar campos del formulario de restablecimiento
function clearResetFormFields() {
    document.getElementById("resetForm").reset();
}

// Cerrar formularios
function closeLoginForm() {
    document.getElementById("loginFormContainer").style.display = "none";
}

function closeResetForm() {
    document.getElementById("resetFormContainer").style.display = "none";
}

// Evento que maneja la inicialización de la página y qué formularios mostrar
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);

    // Manejo de formularios según parámetros en la URL
    if (urlParams.has('show_validation_code')) {
        document.getElementById("resetFormContainer").style.display = "flex";
        toggleForms('resetForm', 'validationForm');
    } else if (urlParams.has('show_new_password')) {
        document.getElementById("resetFormContainer").style.display = "flex";
        toggleForms('validationForm', 'newPasswordForm');
    } else if (urlParams.has('success_password_change')) {
        document.getElementById("loginFormContainer").style.display = "flex";
        document.getElementById("resetFormContainer").style.display = "none";
        document.getElementById("newPasswordForm").style.display = "none";
        clearResetParams(); // Limpiar los parámetros
    } else if (urlParams.has('error_reset')) {
        document.getElementById("resetFormContainer").style.display = "flex";
    }
});
