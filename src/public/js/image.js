'use strict'

function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function () {
      const preview = document.getElementById('userImagePreview');
      preview.style.backgroundImage = `url(${reader.result})`;
  };
  reader.readAsDataURL(event.target.files[0]);
}