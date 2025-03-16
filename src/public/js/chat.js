"use strict";

function showEditForm(id) {
    const editForm = document.getElementById(`editForm${id}`);
    if (editForm) {
        editForm.style.display = "block";
    }
}

function hideEditForm(id) {
    const editForm = document.getElementById(`editForm${id}`);
    if (editForm) {
        editForm.style.display = "none";
    }
}

document
    .getElementById("file-upload")
    .addEventListener("change", function (event) {
        const fileInput = event.target;
        const previewLabel = document.getElementById("preview");

        if (fileInput.files.length > 0) {
            previewLabel.textContent = fileInput.files[0].name;
        } else {
            previewLabel.textContent = "画像を追加";
        }
    });

function openRatingModal() {
    const ratingModal = document.getElementById("ratingModal");
    if (ratingModal) {
        ratingModal.style.display = "block";
    } else {
        console.error("ratingModal element not found.");
    }
}

function hideRatingModal() {
    const ratingModal = document.getElementById("ratingModal");
    if (ratingModal) {
        ratingModal.style.display = "none";
    } else {
        console.error("ratingModal element not found.");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const chatForm = document.querySelector("form[action$='chat.store']");
    if (chatForm) {
        chatForm.addEventListener("submit", function () {
            localStorage.removeItem("stored_content");
            document.getElementById("chat-input").value = "";
        });
    }
    const stars = document.querySelectorAll(".review__stars .star");
    const ratingInput = document.getElementById("rating-value");

    stars.forEach((star) => {
        star.addEventListener("click", function () {
            const value = this.getAttribute("data-value");
            ratingInput.value = value;

            stars.forEach((s) => {
                s.classList.remove("selected");
                if (s.getAttribute("data-value") <= value) {
                    s.classList.add("selected");
                }
            });
        });
    });
});

const chatRoomIdInput = document.querySelector('input[name="chatRoomId"]');
if (chatRoomIdInput) {
    const chatRoomId = chatRoomIdInput.value;
    fetch(`/notifications/read/${chatRoomId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            document.querySelectorAll(".notification-count").forEach((el) => {
                el.textContent = data.unread_count > 0 ? data.unread_count : "";
            });
        })
        .catch((error) => console.error("エラー:", error));
}

document.addEventListener("DOMContentLoaded", function () {
    const chatInput = document.getElementById("chat-input");

    if (!chatInput) return;

    chatInput.addEventListener("input", function () {
        const chatRoomId = chatInput.dataset.chatRoomId;
        const url = chatInput.dataset.route;

        if (!chatRoomId || !url) return;

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                chat_room_id: chatRoomId,
                content: chatInput.value,
            }),
        }).catch((error) => console.error("セッション保存エラー:", error));
    });
});