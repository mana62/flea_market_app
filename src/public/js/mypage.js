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
            const preview = document.getElementById("userImagePreview");
            preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    const imgInput = document.getElementById("img");
    if (imgInput) {
        imgInput.addEventListener("change", previewImage);
    }

    function updateNotificationCount() {
        fetch("/notifications/unread-count")
            .then((response) => response.json())
            .then((data) => {
                document
                    .querySelectorAll(".notification-count")
                    .forEach((el) => {
                        el.textContent =
                            data.unread_count > 0 ? data.unread_count : "";
                    });
            })
            .catch((error) => console.error("通知の更新エラー:", error));
    }

    function readNotice(chatRoomId) {
        fetch(`/chat/${chatRoomId}/read`, {
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
                document
                    .querySelectorAll(".notification-count")
                    .forEach((el) => {
                        el.textContent =
                            data.unread_count > 0 ? data.unread_count : "";
                    });
            })
            .catch((error) => console.error("通知の既読処理エラー:", error));
    }

    const chatRoomIdInput = document.querySelector('input[name="chatRoomId"]');
    if (chatRoomIdInput) {
        const chatRoomId = chatRoomIdInput.value;
        readNotice(chatRoomId);
    }

    const sendButton = document.querySelector(".message-send__button");
    if (sendButton) {
        sendButton.addEventListener("click", () => {
            if (chatRoomIdInput) {
                readNotice(chatRoomIdInput.value);
            }
        });
    }

    updateNotificationCount();
});
