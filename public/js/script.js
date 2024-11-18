document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.container');
    const signUpBtn = document.querySelector('#sign-up-btn');
    const signInBtn = document.querySelector('#sign-in-btn');

    signUpBtn.addEventListener('click', () => {
        container.classList.add('sign-up-mode');
    });

    signInBtn.addEventListener('click', () => {
        container.classList.remove('sign-up-mode');
    });

    // Mostrar el formulario automáticamente si hay errores
    const alertError = document.querySelector('.alert-error');
    if (alertError) {
        container.classList.add('sign-up-mode');
    }
});
