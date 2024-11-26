function showPaymentOptions() {
    document.getElementById("payment-options").style.display = "block";
}

function calculateTotal() {
    const subscriptionPrice = 1200;
    const extraCharges = 200;

    const total = subscriptionPrice + extraCharges;

    document.getElementById("subscription-price").innerText = `Rs. ${subscriptionPrice}`;
    document.getElementById("extra-charges").innerText = `Rs. ${extraCharges}`;
    document.getElementById("total-amount").innerText = `Rs. ${total}`;
}

function addNewPaymentMethod() {
    const paymentType = document.getElementById("payment-type").value;
    const cardNumber = document.getElementById("card-number").value;
    const expiryDate = document.getElementById("expiry-date").value;
    const cvv = document.getElementById("cvv").value;

    alert(`Payment method added: ${paymentType} - ${cardNumber}`);
    // Here you can add functionality to save the payment method to local storage or backend

    // Reset form
    document.getElementById("payment-type").selectedIndex = 0;
    document.getElementById("card-number").value = "";
    document.getElementById("expiry-date").value = "";
    document.getElementById("cvv").value = "";
}



window.onload = calculateTotal;



