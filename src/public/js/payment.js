'use strict';

document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
    const paymentMethod = this.value;
    const itemPrice = Number(document.querySelector('.item-detail__price').dataset.price);

    let fee = 0;
    if (paymentMethod === 'convenienceStore') {
      fee = 200;
    }

    const totalPrice = itemPrice + fee;
    document.querySelector('.item-detail__price').innerText = `¥${totalPrice.toLocaleString()}`;
  });
});
