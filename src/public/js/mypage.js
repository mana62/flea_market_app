"use strict";

// HTMLが読み込まれた後に実行する
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".tabs a").forEach((tab) => {
        tab.addEventListener("click", (event) => {
            // デフォルトのリンク動作を無効化
            event.preventDefault();
            // リンクのURLを取得
            const url = tab.getAttribute("href");
            // ページを指定したURLに遷移
            window.location.href = url;
        });
    });

    // 画像プレビューを表示する関数
    function previewImage(event) {
        // FileReaderオブジェクトを作成
        const reader = new FileReader();
        reader.onload = function () {
            // プレビュー表示用の要素を取得
            const preview = document.getElementById('userImagePreview');
            // 選択した画像を表示
            preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
        };
        // 選択したファイルを読み込む
        reader.readAsDataURL(event.target.files[0]);
    }

    // ファイル選択時にpreviewImage関数を呼び出す
    const imgInput = document.getElementById('img');
    if (imgInput) {
        imgInput.addEventListener('change', previewImage);
    }
});
