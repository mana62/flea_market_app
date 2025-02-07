"use strict";

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById("userImagePreview");
        const base64ImageInput = document.getElementById("imgBase64");
        preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
        base64ImageInput.value = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
