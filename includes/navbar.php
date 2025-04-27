<!-- fonts MONTSERATT -->
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
    <ul>
        <a href="residentials"><li>Residential</li></a>
        <a href="commercials"><li>Commercial</li></a>
        <a href="industrial"><li>Industrial</li></a>
        <a href="lands"><li>Lands</li></a>
        <a href="sell"><li>Sell with us</li></a>
        <a href="about-us"><li>About us</li></a>
        
        <!-- Profile Link (hidden if not logged in) -->
        <a href="profile" id="profileLink" style="display: none;"><li><i class="fa-solid fa-user"></i></li></a>
        
        <a href="favourive"><li><i class="fa-solid fa-house"></i></li></a>
        <a href="call-us"><li><i class="fa-solid fa-phone"></i></li></a>
        
        <!-- Login Button (hidden if logged in) -->
        <a href="login" id="loginBtn" style="display: none;"><li><i class="fa-solid fa-sign-in-alt"></i> <span id="loginText">Login</span></li></a>
    </ul>
</nav>

<!-- JavaScript for Navbar Logic -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const links = document.querySelectorAll("nav ul a"); // Select all <a> tags inside the <nav> element
        const currentPath = window.location.pathname; // Get the current URL path

        // Add 'nav-active' class to the active link based on the current URL
        links.forEach(link => {
            if (currentPath.includes(link.getAttribute("href"))) {
                link.classList.add("nav-active");
            }
        });

        // Navbar elements
        const loginBtn = document.getElementById("loginBtn");
        const profileLink = document.getElementById("profileLink");
        const loginText = document.getElementById("loginText");

        // Retrieve token and userName from localStorage
        const token = localStorage.getItem("token");

        // Check if the token exists to determine if the user is logged in
        if (token) {
            profileLink.style.display = "block"; // Show Profile Icon
            loginBtn.style.display = "none";     // Hide Login Button

            // Optionally, get and display the user's name
            const userName = localStorage.getItem("userName");

            // Display the username or "Profile" as button text
            if (userName) {
                loginText.textContent = `Welcome, ${userName}`;
            } else {
                loginText.textContent = "Profile";
            }

            // Add event listener to the profile link for logout functionality
            profileLink.addEventListener("click", logout);
        } else {
            profileLink.style.display = "none";  // Hide Profile Icon
            loginBtn.style.display = "block";    // Show Login Button
            loginText.textContent = "Login";     // Set Login button text
        }
    });

    // Logout function to clear the token and user data, then reload the page
    function logout() {
        localStorage.removeItem("token");
        localStorage.removeItem("userName");
        window.location.reload(); // Reload page to update navbar
    }
</script>
