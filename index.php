<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- External Stylesheets -->
    <link rel="stylesheet" href="./styles/commonStyles.css">
    <link rel="stylesheet" href="./styles/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <title>Home</title>
</head>

<body>
    <?php include './includes/navbar.php'; ?>

    <!-- Banner Section -->
    <section class="section1 justify-centre">
        <div class="banner">
            <p class="banner-context">FIND THE PERFECT PLACE TO CALL HOME.</p>
        </div>
    </section>

    <!-- Explore Listings Section -->
    <section class="section2 justify-centre">
        <p class="section2-title">Explore listings</p>
        <p class="section2-context subtitle-size">
            Take a deep dive and browse homes for sale, original apartment photos, 
            resident reviews, and local insights to find what is right for you.
        </p>
    </section>

    <?php
    // Database connection and functions
    include 'includes/connection.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Function to render image slider
    function renderImageSlider($images) {
        ?>
        <section class="justify-centre">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php while ($image = $images->fetch_assoc()): ?>
                        <div class="swiper-slide">
                            <div class="image-slide" 
                                 style="background-image: url('<?php echo htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8'); ?>');">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="swiper-button-prev next-buttons"></div>
                <div class="swiper-button-next next-buttons"></div>
                <div class="swiper-pagination"></div>
            </div>
        </section>
        <?php
    }

    // Function to render property card
    function renderPropertyCard($property, $images, $isRental = false) {
        ?>
        <div class="cards-container">
            <a href="">
            <div class="property-card">
                <?php renderImageSlider($images); ?>
                <h3><?php echo htmlspecialchars($property['property_type'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <?php if (!$isRental): ?>
                    <div class="display-flex homesize">
                        <p class="card-square"></p>
                        <p><?php echo htmlspecialchars($property['homeSize'], ENT_QUOTES, 'UTF-8'); ?> SQFT</p>
                    </div>
                <?php endif; ?>
                <p>Ksh <?php echo htmlspecialchars($property['price'], ENT_QUOTES, 'UTF-8'); ?>
                    <?php echo $isRental ? ' /month' : ''; ?></p>
                <p><i class="fa-solid fa-location-dot"></i> 
                   <?php echo htmlspecialchars($property['description'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            </a>
        </div>
        <?php
    }

    // Function to fetch and render properties
    function renderProperties($conn, $listingType, $limit, $isRental = false) {
        $query = "SELECT * FROM properties WHERE listing_type = ? LIMIT ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $listingType, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $imageQuery = "SELECT * FROM property_images WHERE property_id = ?";
        
        while ($property = $result->fetch_assoc()) {
            $stmt = $conn->prepare($imageQuery);
            $stmt->bind_param("i", $property['id']);
            $stmt->execute();
            $images = $stmt->get_result();
            renderPropertyCard($property, $images, $isRental);
        }
    }
    ?>

    <!-- Property Cards Section (Sales) -->
    <section class="section3 justify-centre">
        <div class="property-cards-wrapper">
            <?php renderProperties($conn, 'sale', 12); ?>
        </div>
        <div class="justify-centre">
            <a href="" class="a-button">View More Listings</a>
        </div>
    </section>

    <!-- Property Cards Section (Rentals) -->
    <section class="section4 justify-centre margin-top40">
    <p class="styled-heading">Rentals</p>
        <div class="property-cards-wrapper">
            <?php renderProperties($conn, 'rental', 6, true); ?>
        </div>
        <div class="justify-centre">
            <a href="" class="a-button">View More Rentals</a>
        </div>
    </section>

    

    <section class="section5 margin-top40">
    <p class="justify-centre transparent-p">Simple. Transparent. Hassle-Free  <b class="transparent-b"> Transactions.</b></p>
    <p class="justify-centre transparent-p2">We're with you in every step of finding your dream home.</p>
    <div class="transparent-card-container justify-centre margin-top30">
        <div class="transparent-card justify-centre">
            <p class="picture picture1"></p>
            <p class="title">Buying Process</p>
            <p class="centext">
                See how the buying process
                works so you know exactly
                what to expect every step of
                the way.
            </p>
            <a href="" class="a-button">Read more</a>
        </div>
        <div class="transparent-card justify-centre">
            <p class="picture picture2"></p>
            <p class="title">Selling Process</p>
            <p class="centext">
                Learn how to sell your home
                smoothly and get the best deal
                with our step-by-step guide.
            </p>
            <a href="" class="a-button">Read more</a>
        </div>
        <div class="transparent-card justify-centre">
            <p class="picture picture3"></p>
            <p class="title">Renting Process</p>
            <p class="centext">
                Find out how easy it is to rent
                your ideal home with our
                simple and transparent
                process.
            </p>
            <a href="" class="a-button">Read more</a>
        </div>
    </div>
    </section>
    <section class="section6 margin-top50">
        <p class="title margin-bottom30">FEATURED </p>
        <div class="amenities margin-bottom30">
            <p>Luxury Modern Villa</p>
            <p><i class="fa-solid fa-location-dot"></i> Kitengela-Namanga Rd</p>
            <p>KES 20,000,000</p>
        </div>
        <div class="container">
          <div class="photo-grid">
            <div class="photo large"><img src="./images/featured5.jpg" alt="Main House" /></div>
            <div class="photo"><img src="./images/featured1.jpg" alt="Interior 1" /></div>
            <div class="photo"><img src="./images/featured2.jpg" alt="Interior 2" /></div>
            <div class="photo"><img src="./images/featured3.jpg" alt="Library" /></div> 
            <div class="photo"><img src="./images/featured6.jpg" alt="Interior 1" /></div>
            <div class="photo"><img src="./images/featured7.jpg" alt="Interior 2" /></div>
            <div class="photo"><img src="./images/featured8.jpg" alt="Library" /></div>  
          </div>
        </div>
    </section>

    <?php
// db connection (adjust your credentials)
$sql = "SELECT name, text, rating FROM feeback LIMIT 6";
$result = $conn->query($sql);
?>

<section class="section7">
  <p class="styled-heading">Clients feedback</p>
  <div class="cards-container display-flex margin-left50 margin-top40">
        <div class="rating-card">
            <p class="feedback-text">
            See how the buying process works so you know exactly what to expect every step of the way.
            </p>
            <p class="name">--- Nancy Jeruto</p>
            <p class="stars justify-centre">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
            </p>
        </div>
        <div class="rating-card">
                <p class="feedback-text">
                See how the buying process works so you know exactly what to expect every step of the way.
                </p>
                <p class="name">--- Nancy Jeruto</p>
                <p class="stars justify-centre">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </p>
        </div>
        <form action="">
            <input type="text" placeholder="Email">
            <textarea name="" id="" placeholder="Your feedback here"></textarea>
            <button type="submit" class="a-button">Submit</button>
        </form>
    </div>
</section>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="./scripts/swiper.js"></script>
</body>
</html>