"use strict";

document.querySelectorAll('.toggle-password').forEach(button => {
  button.addEventListener('click', function() {
    const passwordInput = document.getElementById(this.getAttribute('data-target'));
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    this.textContent = type === 'password' ? '🙉' : '🙈';
  });
});
