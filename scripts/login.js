document.addEventListener("DOMContentLoaded", function () {
    const submitBtn = document.getElementById("submitBtn");
    const emailInput = document.getElementById("loginEmail");
    const passwordInput = document.getElementById("loginPassword");
    const resultContainer = document.getElementById("loginResult");

    submitBtn.addEventListener("click", function (e) {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        fetch("./loginProcess", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                email: email,
                password: password
            })
        })
        .then(res => res.json())
        .then(data => {
            resultContainer.textContent = data.message;
            resultContainer.style.color = data.success ? "green" : "red";

            if (data.success) {
                setTimeout(() => {
                    window.location.href = "./index"; // Adjust destination as needed
                }, 1000);
            }
        })
        .catch(err => {
            console.error("Fetch Error:", err);
            resultContainer.textContent = "Something went wrong. Please try again.";
            resultContainer.style.color = "red";
        });
    });
});
