"use strict";

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById("userImagePreview");
        const imgBase64Input = document.getElementById("imgBase64");

        // プレビューに画像を表示
        preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;

        // Base64形式のデータをhidden inputに設定
        imgBase64Input.value = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]); // ファイルをBase64形式で読み込む
}
