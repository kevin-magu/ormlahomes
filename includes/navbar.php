<?php
session_start();
include './includes/connection.php';
?>

<!-- fonts MONTSERRAT -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<!-- icons -->
<script src="https://kit.fontawesome.com/e4c074505f.js" crossorigin="anonymous"></script>

<!-- Navbar -->
<nav>
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
        
        <a href="/orlmahomes/favorite-properties"><li><i class="fa-solid fa-house"></i> Favourites <span id="fav-count"></span></li></a>
        <a href="tel:+254796257269"><li><i class="fa-solid fa-phone"></i> Call us</li></a>
        <!-- Login/Profile Links will be controlled by JS -->
        <span id="auth-links"></span>
        <a href="/orlmahomes/aboutus"><li>About us</li></a>
    </ul>
    <div class="menu-bar" id="menuBar">
        <div class="line line1"></div>
        <div class="line line2"></div>
        <div class="line line3"></div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    const authLinks = document.getElementById('auth-links');

    const menuBar = document.getElementById('menuBar');
    const navMenu = document.getElementById('nav-links');

    menuBar.addEventListener('click', () => {
        menuBar.classList.toggle('active'); 
        navMenu.classList.toggle('show');   
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1540) {
            navMenu.classList.remove('show');
            menuBar.classList.remove('active');
        }
    });
    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
    const isClickInsideMenu = navMenu.contains(e.target);
    const isClickOnMenuIcon = menuBar.contains(e.target);

    if (!isClickInsideMenu && !isClickOnMenuIcon) {
        navMenu.classList.remove('show');
        menuBar.classList.remove('active');
    }
    })


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

  //get favorite count
  window.updateFavoriteCount= function () {
    fetch('/orlmahomes/get_favorite_count.php')
        .then(res => res.json())
        .then(data => {
            const countElement = document.getElementById('fav-count');
            if (countElement) {
                countElement.textContent = data.count;
            }
        });
    }
    updateFavoriteCount();
})
</script>
