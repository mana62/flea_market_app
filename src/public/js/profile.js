'use strict'

//画像プレビューを表示する関数
function previewImage(event) {
  //FileReaderオブジェクトを作成
  const reader = new FileReader();
  reader.onload = function () {
      //プレビュー表示用の要素を取得
      const preview = document.getElementById('userImagePreview');
      //選択した画像を表示
      preview.innerHTML = `<img src="${reader.result}" alt="選択した画像">`;
  };
  //選択したファイルを読み込む
  reader.readAsDataURL(event.target.files[0]);
}