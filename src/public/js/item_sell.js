"use strict";

//商品画像のプレビューを表示
function previewImage(event) {
    //ファイル入力要素
    const file = event.target.files[0];
    //ファイル名表示要素
    const fileNameElement = document.getElementById("file-name");
    //ラベル要素
    const imgLabel = document.getElementById("img-label");

    if (file) {
        //ファイル名を表示
        fileNameElement.textContent = file.name;
        //ラベルを非表示
        imgLabel.style.display = "none";
        //ファイル名表示を有効化
        fileNameElement.style.display = "block";
    } else {
        //ファイル名をクリア
        fileNameElement.textContent = "";
        //ラベルを表示
        imgLabel.style.display = "block";
        //ファイル名表示を非表示
        fileNameElement.style.display = "none";
    }
}
