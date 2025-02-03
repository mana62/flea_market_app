"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const fileInput = document.getElementById("img");
    const imgPreview = document.getElementById("itemImagePreview");
    const imgBase64Input = document.getElementById("imgBase64");
    const form = document.querySelector(".form");

    // 画像選択時の処理
    fileInput.addEventListener("change", (event) => {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const base64Data = e.target.result;

                // 画像プレビューの表示
                imgPreview.innerHTML = `<img src="${base64Data}" alt="選択した画像" style="max-width: 200px; height: auto; margin-right: 10px;">`;

                // sessionStorage に Base64 画像データを保存
                sessionStorage.setItem("itemImage", base64Data);

                // hidden input に Base64 データをセット
                imgBase64Input.value = base64Data;
            };
            reader.readAsDataURL(file);
        }
    });

    // **バリデーションエラー時に sessionStorage または Laravel のセッションから画像を復元**
    const storedImgPreview = sessionStorage.getItem("itemImage") || imgBase64Input.value;
    if (storedImgPreview) {
        imgPreview.innerHTML = `<img src="${storedImgPreview}" alt="選択した画像" style="max-width: 200px; height: auto; margin-right: 10px;">`;
        imgBase64Input.value = storedImgPreview;
    }

    // **フォーム送信時に sessionStorage を削除**
    form.addEventListener("submit", () => {
        sessionStorage.removeItem("itemImage");
    });

    // **リロード（F5）やページ遷移時に sessionStorage を削除**
    window.addEventListener("beforeunload", () => {
        sessionStorage.removeItem("itemImage");
    });

    // **フォーム送信成功時に sessionStorage と Laravel のセッションを削除**
    if (document.querySelector(".message-session")) {
        sessionStorage.removeItem("itemImage");
    }
});


// 販売価格入力欄に「¥」を適切に追加する関数
function formatPrice(inputElement) {
    let value = inputElement.value.replace(/[^\d]/g, ''); // 数字以外を削除
    if (value) {
        inputElement.value = `${Number(value).toLocaleString()}`;
    } else {
        inputElement.value = '';
    }
}

