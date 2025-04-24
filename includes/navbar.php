<!-- fonts MONTSERATT -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<!-- icons -->
    <script src="https://kit.fontawesome.com/e4c074505f.js" crossorigin="anonymous"></script>
    <!-- icons -->
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
         <a href="profile"><li><i class="fa-solid fa-user"></i></li></a>
         <a href="favourive"><li><i class="fa-solid fa-house"></i></li></a>
         <a href="call-us"><li><i class="fa-solid fa-phone"></i></li></a>
     </ul>
 </nav>

 <script>
    document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll("nav ul a"); // Select all <a> tags inside the <nav> element
    const currentPath = window.location.pathname; // Get the current URL path

    links.forEach(link => {
        // Check if the current link's href matches the current URL path
        if (currentPath.includes(link.getAttribute("href"))) {
            link.classList.add("nav-active"); // Add the 'nav-active' class to the matched link
        }
    });
});

 </script>