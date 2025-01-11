"use strict";

function previewImage(event) {
    const input = event.target;
    const fileNameElement = document.getElementById("file-name");
    const imgLabel = document.getElementById("img-label");
    if (input.files && input.files[0]) {
        fileNameElement.textContent = input.files[0].name;
        imgLabel.style.display = "none";
        fileNameElement.style.display = "block";
    } else {
        fileNameElement.textContent = "";
        imgLabel.style.display = "block";
        fileNameElement.style.display = "none";
    }
}
