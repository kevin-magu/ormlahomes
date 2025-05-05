<?php
session_start();
?>

<!-- fonts MONTSERRAT -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<!-- icons -->
<script src="https://kit.fontawesome.com/e4c074505f.js" crossorigin="anonymous"></script>

<!-- Navbar -->
<nav id="page-top">
    <a href="/orlmahomes" class="nav-logo-container">
        <div class="nav-logo"></div>
        <p class="copany-name">ORLMA HOMES & PROPERTIES</p>
    </a>
    <ul id="nav-links">
        <a href="/orlmahomes/residentials"><li>Residential</li></a>
        <a href="/orlmahomes/commercials"><li>Commercial</li></a>
        <a href="/orlmahomes/industrial"><li>Industrial</li></a>
        <a href="/orlmahomes/lands"><li>Lands</li></a>
        <a href="/orlmahomes/sell"><li>Sell with us</li></a>
        <a href="/orlmahomes/about-us"><li>About us</li></a>

        <!-- Login/Profile Links will be controlled by JS -->
        <span id="auth-links"></span>

        <a href="/orlmahomes/favourite"><li><i class="fa-solid fa-house"></i></li></a>
        <a href="/orlmahomes/call-us"><li><i class="fa-solid fa-phone"></i></li></a>
    </ul>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // ✅ Define global login status
    
    console.log("window.isLoggedIn =", window.isLoggedIn);

    const token = localStorage.getItem('token');
    const authLinks = document.getElementById('auth-links');

    if (!token) {
        // If no token, show Login link
        authLinks.innerHTML = `
            <a href="/orlmahomes/login"><li><i class="fa-solid fa-sign-in-alt"></i> Login</li></a>
        `;
    } else {
        // Validate token
        fetch('/orlmahomes/validate_token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Token valid, show Profile
                authLinks.innerHTML = `
                    <a href="/orlmahomes/profile"><li><i class="fa-solid fa-user"></i> Profile</li></a>
                `;
            } else {
                // Token invalid
                localStorage.removeItem('token');
                authLinks.innerHTML = `
                    <a href="/orlmahomes/login"><li><i class="fa-solid fa-sign-in-alt"></i> Login</li></a>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            localStorage.removeItem('token');
            authLinks.innerHTML = `
                <a href="/orlmahomes/login"><li><i class="fa-solid fa-sign-in-alt"></i> Login</li></a>
            `;
        });
    }

    // Highlight current nav link
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll("#nav-links a");
    navLinks.forEach(link => {
        if (currentPath === link.getAttribute("href")) {
            link.classList.add("active");
        }
    });
});
</script>
