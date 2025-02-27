function showForm(formType) {
    const loginForm = document.getElementById('login');
    const registerForm = document.getElementById('register');
    const tabButtons = document.querySelectorAll('.tab-button');

    if (formType === 'login') {
        loginForm.classList.add('active');
        registerForm.classList.remove('active');
        tabButtons[0].classList.add('active');
        tabButtons[1].classList.remove('active');
    } else {
        loginForm.classList.remove('active');
        registerForm.classList.add('active');
        tabButtons[0].classList.remove('active');
        tabButtons[1].classList.add('active');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    showForm('login');
});
