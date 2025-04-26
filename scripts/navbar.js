document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll("nav ul a");
    const currentPath = window.location.pathname;

    links.forEach(link => {
        if (currentPath.includes(link.getAttribute("href"))) {
            link.classList.add("nav-active");
        }
    });

    const loginBtn = document.getElementById("loginBtn");
    const profileLink = document.getElementById("profileLink");
    const loginText = document.getElementById("loginText");
    const token = localStorage.getItem("token");

    if (token) {
        profileLink.style.display = "block";
        loginText.textContent = "Profile";
    } else {
        loginBtn.style.display = "block";
        loginText.textContent = "Login";
    }

    const userName = localStorage.getItem("userName");
    if (userName) {
        loginText.textContent = `Welcome, ${userName}`;
    }
});

// Optional logout function if you want to use it globally
function logout() {
    localStorage.removeItem("token");
    localStorage.removeItem("userName");
    window.location.href = "login";
}
