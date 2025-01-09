'use strict'

function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function () {
      const preview = document.getElementById('userImagePreview');
      preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
  };
  reader.readAsDataURL(event.target.files[0]);
}