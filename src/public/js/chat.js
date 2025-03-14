"use strict";

// 編集部分の表示
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

// 画像選択時にファイル名を表示
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

// モーダルウィンドウを開く
function openRatingModal() {
    const ratingModal = document.getElementById("ratingModal");
    if (ratingModal) {
        ratingModal.style.display = "block";
    } else {
        console.error("ratingModal element not found.");
    }
}

// モーダルウィンドウを閉じる
function hideRatingModal() {
    const ratingModal = document.getElementById("ratingModal");
    if (ratingModal) {
        ratingModal.style.display = "none";
    } else {
        console.error("ratingModal element not found.");
    }
}

// DOMContentLoaded イベントリスナー
document.addEventListener("DOMContentLoaded", function () {
    // テキストの保存
    const chatForm = document.querySelector("form[action$='chat.store']");
    if (chatForm) {
        chatForm.addEventListener("submit", function () {
            localStorage.removeItem("stored_content");
            document.getElementById("chat-input").value = "";
        });
    }

    // モーダルウィンドウの中の評価
    const stars = document.querySelectorAll(".review__stars .star");
    const ratingInput = document.getElementById("rating-value");

    stars.forEach((star) => {
        star.addEventListener("click", function () {
            const value = this.getAttribute("data-value");
            ratingInput.value = value;

            // クリックした星まで色を付ける
            stars.forEach((s) => {
                s.classList.remove("selected");
                if (s.getAttribute("data-value") <= value) {
                    s.classList.add("selected");
                }
            });
        });
    });
});

// 未読メッセージを既読にする
const chatRoomIdInput = document.querySelector('input[name="chatRoomId"]');
if (chatRoomIdInput) {
    const chatRoomId = chatRoomIdInput.value;
    fetch(`/notifications/read/${chatRoomId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Content-Type": "application/json",
        },
    })
    .then(response => response.json())
    .then(data => {
        console.log("未読メッセージを既読にしました", data);
        // 未読カウントを更新
        document.querySelectorAll(".notification-count").forEach(el => {
            el.textContent = data.unread_count > 0 ? data.unread_count : "";
        });
    })
    .catch(error => console.error("エラー:", error));
}

// 取引完了ボタンのイベント
const completeButton = document.querySelector(".complete-button");
if (completeButton) {
    completeButton.addEventListener("click", function () {
        // 取引完了処理（APIリクエストなど）
        console.log("取引完了処理");
    });
}