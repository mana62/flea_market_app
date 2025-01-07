"use strict";

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".like-btn").forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            toggleLike(this, this.dataset.itemId);
        });
    });
});

function toggleLike(button, itemId) {
    const isLiked = button.classList.contains("liked");

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
            if (data.liked) {
                button.classList.add("liked");
            } else {
                button.classList.remove("liked");
            }
            document.querySelector(
                `#like-count[data-item-id="${itemId}"]`
            ).innerText = data.likesCount;
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("エラーが発生しました");
        });
}
