document.addEventListener("DOMContentLoaded", function () {
    const submitBtn = document.getElementById("submitBtn");
    const nameInput = document.getElementById("registerName");
    const emailInput = document.getElementById("registerEmail");
    const mobileInput = document.getElementById("registerMobile");
    const passwordInput = document.getElementById("registerPassword");
    const confirmPasswordInput = document.getElementById("registerConfirmPassword");
    const resultContainer = document.getElementById("registrationResult");

    submitBtn.addEventListener("click", function (e) {
        e.preventDefault();

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const mobile = mobileInput.value.trim();
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        fetch("./registerProcess", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                name: name,
                email: email,
                mobile: mobile,
                password: password,
                confirmPassword: confirmPassword
            })
        })
        .then(res => res.json())
        .then(data => {
            resultContainer.textContent = data.message;
            resultContainer.style.color = data.success ? "green" : "red";
        })
        .catch(err => {
            console.error("Fetch Error:", err);
            resultContainer.textContent = "Something went wrong. Please try again.";
            resultContainer.style.color = "red";
        });
    });
});
