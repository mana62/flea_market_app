"use strict";

// Stripeの初期化
const stripe = Stripe(stripePublicKey); // Bladeテンプレートから取得
const elements = stripe.elements();
const cardElement = elements.create("card");
cardElement.mount("#card-element"); //#card-elementというIDを持つHTML要素にカード入力フィールドを設置する

// カード入力エラーを表示
cardElement.addEventListener("change", function (event) {
    const displayError = document.getElementById("card-errors");
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = "";
    }
});

// フォームの送信処理
const form = document.getElementById("payment-form");
form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const submitButton = form.querySelector(".payment-button__submit");
    submitButton.disabled = true;
    submitButton.textContent = "処理中...";


    try {
        // Stripeでカード決済を確認
        const result = await stripe.confirmCardPayment(clientSecret, {
            payment_method: { card: cardElement },
        });

        if (result.error) {
            console.error(result.error);
            alert("決済に失敗しました: " + result.error.message);
            submitButton.disabled = false;
            submitButton.textContent = "支払う";
            return;
        }

     // サーバーへ支払い情報を送信
        const response = await axios.post(`/payment/${itemId}`, {
            purchase_id: purchaseId,
            payment_intent_id: result.paymentIntent.id,
            amount: parseInt(document.getElementById("amount").textContent.replace('円', '').replace(/,/g, '')),
            currency: 'jpy',
        }, {
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
        });

        // サーバーのレスポンスを処理
        if (response.data.succeeded) {
            window.location.href = "/thanks-buy";
        } else {
            alert("支払い情報の保存に失敗しました: " + response.data.message);
        }
    } catch (error) {
        console.error("エラー:", error);
        if (error.response) {
            console.error("サーバーからのレスポンス:", error.response.data);
            alert("サーバーエラーが発生しました: " + error.response.data.message);
        } else {
            alert("リクエストの送信中にエラーが発生しました。");
        }
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = "支払う";
    }
});