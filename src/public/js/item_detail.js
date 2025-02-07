"use strict";

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".like-btn").forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            toggleLike(button, button.dataset.itemId);
        });
    });
});

function toggleLike(button, itemId) {
    fetch(`/item/${itemId}/like`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            button.classList.toggle("liked", data.liked);
            const likeCountElement = document.querySelector(
                `p[data-item-id="${itemId}"]`
            );
            if (likeCountElement) {
                likeCountElement.innerText = data.likesCount;
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("エラーが発生しました");
        })
        .finally(() => {
            button.disabled = false;
        });
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById("userImagePreview");
        preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
    };
    reader.readAsDataURL(event.target.files[0]);
}
