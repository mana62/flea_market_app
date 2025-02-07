"use strict";

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".tabs a").forEach((tab) => {
        tab.addEventListener("click", (event) => {
            event.preventDefault();
            const url = tab.getAttribute("href");
            window.location.href = url;
        });
    });

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const preview = document.getElementById('userImagePreview');
            preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    const imgInput = document.getElementById('img');
    if (imgInput) {
        imgInput.addEventListener('change', previewImage);
    }
});
