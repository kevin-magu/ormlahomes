<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Orlma Homes & Properties</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./styles/aboutus.css">
    <link rel="stylesheet" href="./styles/commonStyles.css">
</head>
<body>
    <?php include './includes/navbar.php' ?>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1>About Orlma Homes & Properties</h1>
            <p>Your trusted partner in real estate</p>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <h2>Who We Are</h2>
                <p>
                    Orlma Homes & Properties is a leading real estate agency dedicated to helping clients buy, rent, and sell properties with ease. 
                    With years of experience in the industry, we provide expert guidance, personalized service, and a wide selection of properties 
                    to meet every need.
                </p>
                <p>
                    Our mission is to make real estate transactions seamless, transparent, and stress-free for our clients. Whether you're looking 
                    for a dream home, commercial space, or land investment, we are here to help you every step of the way.
                </p>
            </div>
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Luxury Home">
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <h2>Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <i class="fas fa-home"></i>
                    <h3>Buy Houses</h3>
                    <p>Find your dream home from our extensive listings.</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-key"></i>
                    <h3>Rent Houses</h3>
                    <p>Affordable rental properties for families and individuals.</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>Sell Houses</h3>
                    <p>Get the best market value for your property.</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-tree"></i>
                    <h3>Buy & Sell Lands</h3>
                    <p>Prime land for development and investment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Property Categories -->
    <section class="categories-section">
        <div class="container">
            <h2>Our Property Categories</h2>
            <div class="categories-grid">
                <div class="category-card residential">
                    <h3>Residential</h3>
                    <p>Luxury homes, apartments, and family houses.</p>
                </div>
                <div class="category-card commercial">
                    <h3>Commercial</h3>
                    <p>Office spaces, retail shops, and business properties.</p>
                </div>
                <div class="category-card industrial">
                    <h3>Industrial</h3>
                    <p>Warehouses, factories, and industrial land.</p>
                </div>
                <div class="category-card lands">
                    <h3>Lands</h3>
                    <p>Prime land for sale and development.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Find Your Dream Property?</h2>
            <p>Contact us today for expert advice and personalized service.</p>
            <a href="tel:+254796257269" class="cta-button">Get in Touch</a>
            <p>Email: enquire@orlmahomes.com</p>
        </div>
    </section>

<?php include './includes/footer.php' ?>
</body>
</html>