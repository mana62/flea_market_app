"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const paymentSelect = document.getElementById("payment-method");
    const itemDetailPaymentMethod = document.getElementById("itemDetailPaymentMethod");

    // セッションストレージから値を取得して反映
    const savedPaymentMethod = sessionStorage.getItem("payment_method");

    if (savedPaymentMethod) {
        paymentSelect.value = savedPaymentMethod;
        updatePaymentMethodDisplay(savedPaymentMethod);
    } else {
        updatePaymentMethodDisplay(paymentSelect.value);
    }

    // 支払い方法を変更したら `sessionStorage` に保存
    paymentSelect.addEventListener("change", function () {
        sessionStorage.setItem("payment_method", this.value);
        updatePaymentMethodDisplay(this.value);
    });

    function updatePaymentMethodDisplay(value) {
        itemDetailPaymentMethod.innerText =
            value === "convenience-store" ? "コンビニ払い" :
            value === "card" ? "カード払い" :
            "未選択";
    }

    // 購入ボタンを押したときに `sessionStorage` をクリア
    const purchaseButton = document.querySelector(".item-purchase__button-submit");
    sessionStorage.clear();
});
