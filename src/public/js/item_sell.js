"use strict";

// 商品画像のプレビューを表示 (Base64形式で処理)
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('img-preview');
    const fileNameElement = document.getElementById("file-name");
    const imgLabel = document.getElementById("img-label");

    if (file) {
        const reader = new FileReader();
        reader.onload = function () {
            preview.src = reader.result; // Base64データをプレビューに設定
            preview.style.display = "block"; // 画像を表示
        };
        reader.readAsDataURL(file); // Base64形式に変換

        // ファイル名を表示
        fileNameElement.textContent = file.name;
        imgLabel.style.display = "none";
        fileNameElement.style.display = "block";
    } else {
        // プレビューをリセット
        preview.src = "";
        preview.style.display = "none";
        fileNameElement.textContent = "";
        imgLabel.style.display = "block";
        fileNameElement.style.display = "none";
    }
}

// ページロード時にセッションから画像とファイル名を表示
window.addEventListener('load', function () {
    const base64Image = "{{ session('base64_image') }}";
    const fileName = "{{ session('file_name') }}"; // ファイル名もセッションから取得
    const preview = document.getElementById('img-preview');
    const fileNameElement = document.getElementById("file-name");
    const imgLabel = document.getElementById("img-label");

    if (base64Image) {
        preview.src = base64Image;
        preview.style.display = "none"; // 画像を非表示
        fileNameElement.textContent = fileName;
        fileNameElement.style.display = "block"; // ファイル名を表示
        imgLabel.style.display = "none";
    } else if (preview.src) {
        preview.style.display = "none"; // 画像を非表示
    }
});

// 販売価格入力欄に「¥」を適切に追加する関数
function formatPrice(inputElement) {
    let value = inputElement.value;
    // 数値かどうかをチェック
    if (!isNaN(value) && value !== '') {
        // ¥を付けて表示
        inputElement.value = `${value}`;
    } else {
        // 無効な値の場合は空にする
        inputElement.value = '';
    }
    // 「¥」を除去して数値部分だけを取得
    value = value.replace(/¥/g, '').trim();

}
