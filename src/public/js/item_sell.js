"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const fileInput = document.getElementById("img");
    const imgPreview = document.getElementById("itemImagePreview");
    const imgBase64Input = document.getElementById("imgBase64");
    const form = document.querySelector(".form");

    fileInput.addEventListener("change", (event) => {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const base64Data = e.target.result;

                imgPreview.innerHTML = `<img src="${base64Data}" alt="選択した画像">`;
                sessionStorage.setItem("itemImage", base64Data);
                imgBase64Input.value = base64Data;
            };
            reader.readAsDataURL(file);
        }
    });

    const storedImgPreview =
        sessionStorage.getItem("itemImage") || imgBase64Input.value;
    if (storedImgPreview) {
        imgPreview.innerHTML = `<img src="${storedImgPreview}" alt="選択した画像">`;
        imgBase64Input.value = storedImgPreview;
    }

    form.addEventListener("submit", () => {
        sessionStorage.removeItem("itemImage");
    });

    window.addEventListener("beforeunload", () => {
        sessionStorage.removeItem("itemImage");
    });

    if (document.querySelector(".message-session")) {
        sessionStorage.removeItem("itemImage");
    }
});

function formatPrice(inputElement) {
    let value = inputElement.value.replace(/[^\d]/g, "");
    if (value) {
        inputElement.value = Number(value).toLocaleString();
    } else {
        inputElement.value = "";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const priceInput = document.getElementById("price");
    if (priceInput) {
        priceInput.addEventListener("input", () => formatPrice(priceInput));
    }
});
