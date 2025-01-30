"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const paymentSelect = document.getElementById("payment-method");
    const itemDetailPaymentMethod = document.getElementById("itemDetailPaymentMethod");

    paymentSelect.addEventListener("change", function () {
        const selectedPaymentMethod = this.value;
        itemDetailPaymentMethod.innerText = selectedPaymentMethod === "convenience-store" ? "コンビニ払い" : "カード払い";
    });
});
