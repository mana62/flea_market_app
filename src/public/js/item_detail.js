"use strict";

//ページ読み込み後にいいねボタンのクリックイベントを設定
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".like-btn").forEach((button) => {
        button.addEventListener("click", (event) => {
            //ページリロードを防止
            event.preventDefault();
            //いいねの切り替え関数を呼び出す
            toggleLike(button, button.dataset.itemId);
        });
    });
});

//いいねの切り替え処理
function toggleLike(button, itemId) {
    // button.disabled = true;

    fetch(`/item/${itemId}/like`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
    })
        .then((response) => response.json())
        .then((data) => {
            button.classList.toggle("liked", data.liked);
            document.querySelector(`#like-count[data-item-id="${itemId}"]`).innerText = data.likesCount;
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("エラーが発生しました");
        })
        .finally(() => {
            button.disabled = false;
        });
}


//プレビュー画像の表示処理
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById("userImagePreview");
        //プレビュー表示
        preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
    };
    //ファイルを読み込む
    reader.readAsDataURL(event.target.files[0]);
}
