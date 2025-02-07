"use strict";

const stripe = Stripe(stripePublicKey);
const elements = stripe.elements();
const cardElement = elements.create("card");
cardElement.mount("#card-element");

cardElement.addEventListener("change", function (event) {
    const displayError = document.getElementById("card-errors");
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = "";
    }
});

const form = document.getElementById("payment-form");
form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const submitButton = form.querySelector(".payment__button-submit");
    submitButton.disabled = true;
    submitButton.textContent = "処理中...";

    try {
        const result = await stripe.confirmCardPayment(clientSecret, {
            payment_method: { card: cardElement },
        });

        if (result.error) {
            console.error(result.error);
            result.error.message;
            submitButton.disabled = false;
            submitButton.textContent = "支払う";
            return;
        }

        const response = await axios.post(
            `/payment/${itemId}`,
            {
                purchase_id: purchaseId,
                payment_intent_id: result.paymentIntent.id,
                amount: parseInt(
                    document
                        .getElementById("amount")
                        .textContent.replace("円", "")
                        .replace(/,/g, "")
                ),
                currency: "jpy",
            },
            {
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            }
        );

        if (response.data.succeeded) {
            window.location.href = "/thanks-buy";
        } else {
            alert("支払い情報の保存に失敗しました: " + response.data.message);
        }
    } catch (error) {
        console.error("エラー:", error);
        if (error.response) {
            console.error("サーバーからのレスポンス:", error.response.data);
            alert(
                "サーバーエラーが発生しました: " + error.response.data.message
            );
        } else {
            alert("リクエストの送信中にエラーが発生しました。");
        }
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = "支払う";
    }
});
