"use strict";

document.querySelectorAll('.toggle-password').addEventListener('click', function() {
  const passwordInput = document.getElementById('password');
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);

  this.textContent = type === 'password' ? '🙉' : '🙈';
});