"use strict";

//ページ読み込み時の処理
document.addEventListener("DOMContentLoaded", () => {
    //支払い方法のセレクトボックス
    const paymentSelect = document.getElementById("payment-method");
    //購入フォーム
    const purchaseForm = document.getElementById("purchase-form");
    //支払い方法を表示する要素
    const itemDetailPaymentMethod = document.getElementById("itemDetailPaymentMethod");

    //支払い方法をフォームデータに追加
    let hiddenPaymentInput = document.createElement("input");
    hiddenPaymentInput.type = "hidden";
    hiddenPaymentInput.name = "payment_method";
    purchaseForm.appendChild(hiddenPaymentInput);

    //支払い方法が変更されたときの処理
    paymentSelect.addEventListener("change", function () {
        const selectedPaymentMethod = this.value;
        //支払い方法を表示
        itemDetailPaymentMethod.innerText = selectedPaymentMethod === "convenience-store" ? "コンビニ払い" : "カード払い";
        //hiddenフィールドに値をセット
        hiddenPaymentInput.value = selectedPaymentMethod; 
    });

    //フォーム送信時の支払い方法未選択エラー処理
    purchaseForm.addEventListener("submit", (e) => {
        if (!paymentSelect.value) {
            e.preventDefault();
            alert("支払い方法を選択してください");
        }
    });
});
