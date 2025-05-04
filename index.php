<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home</title>
   
    <!-- External Stylesheets -->
    <link rel="stylesheet" href="./styles/commonStyles.css" />
    <link rel="stylesheet" href="./styles/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
</head>
<body>

    <?php include './includes/navbar.php'; ; ?>

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
    <div id="userResponse" class="response-box hidden"></div>
    <?php
        include 'includes/connection.php';
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        function renderImageSlider($images) {
            ?>
            <section class="justify-centre">
                <div class="swiper mySwiper" style="position: relative;">
                    <div class="swiper-wrapper">
                        <?php while ($image = $images->fetch_assoc()): ?>
                            <div class="swiper-slide">
                                <div class="image-slide"
                                     style="background-image: url('<?php echo htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8'); ?>');">
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

        function renderPropertyCard($property, $images, $isRental = false) {
            $token = base64_encode("property_" . $property['id']);
            ?>


            <div class="cards-container">
                <div class="property-card" style="position: relative;">
                <i class="fa-regular fa-heart heart-icon" 
                data-property-id="<?php echo htmlspecialchars((int) $property['id'], ENT_QUOTES, 'UTF-8'); ?>"></i>

                    <a href="property?ref=<?= urlencode($token) ?>">
                        <div class="property-card-inner">
                            <?php renderImageSlider($images); ?>
                            <h3><?php echo htmlspecialchars($property['property_type'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <div class="listing-type"><?= htmlspecialchars($property['listing_type']) ?></div>
                            <?php if (!$isRental): ?>
                                <div class="display-flex homesize">
                                    <p class="card-square"></p>
                                    <p><?php echo htmlspecialchars($property['propertySize'], ENT_QUOTES, 'UTF-8'); ?> SQFT</p>
                                </div>
                            <?php endif; ?>
                            <p>Ksh <?php echo number_format((int)$property['price']) ?><?php echo $isRental ? ' /month' : ''; ?></p>
                            <p><i class="fa-solid fa-location-dot"></i>
                            <?php echo htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php echo htmlspecialchars($property['id'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </a>
                </div>
            </div>
            <?php
        }

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

    <!-- Sales Section -->
    <section class="section3 justify-centre">
        <div class="property-cards-wrapper">
            <?php renderProperties($conn, 'For sale', 12); ?>
        </div>
        <div class="justify-centre">
            <a href="./residentials"><button>View more listings</button></a>
        </div>
    </section>

    <!-- Rentals Section -->
    <section class="section4 justify-centre margin-top40">
        <p class="styled-heading">Rentals</p>
        <div class="property-cards-wrapper">
            <?php renderProperties($conn, 'rental', 6, true); ?>
        </div>
        <div class="justify-centre">
            <a href="./residentials"><button>View more rentals</button></a>
        </div>
    </section>

    <!-- Transparent Steps Section -->
    <section class="section5 margin-top40">
        <p class="justify-centre transparent-p">
            Simple. Transparent. Hassle-Free <b class="transparent-b">Transactions.</b>
        </p>
        <p class="justify-centre transparent-p2">
            We're with you in every step of finding your dream home.
        </p>
        <div class="transparent-card-container justify-centre margin-top30">
            <?php
                $steps = [
                    ['title' => 'Buying Process', 'desc' => 'See how the buying process works so you know exactly what to expect every step of the way.', 'imgClass' => 'picture1'],
                    ['title' => 'Selling Process', 'desc' => 'Learn how to sell your home smoothly and get the best deal with our step-by-step guide.', 'imgClass' => 'picture2'],
                    ['title' => 'Renting Process', 'desc' => 'Find out how easy it is to rent your ideal home with our simple and transparent process.', 'imgClass' => 'picture3'],
                ];
                foreach ($steps as $step):
            ?>
                <div class="transparent-card justify-centre">
                    <p class="picture <?php echo $step['imgClass']; ?>"></p>
                    <p class="title"><?php echo $step['title']; ?></p>
                    <p class="centext"><?php echo $step['desc']; ?></p>
                    <a href="" class=""><button>Read more</button></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Featured Property Section -->
    <section class="section6 margin-top50">
        <p class="title margin-bottom30">FEATURED</p>
        <div class="amenities margin-bottom30">
            <p>Luxury Modern Villa</p>
            <p><i class="fa-solid fa-location-dot"></i> Kitengela-Namanga Rd</p>
            <p>KES 20,000,000</p>
        </div>
        <div class="container">
            <div class="photo-grid">
                <?php
                    $featuredImages = ['featured5', 'featured1', 'featured2', 'featured3', 'featured6', 'featured7', 'featured8'];
                    foreach ($featuredImages as $index => $img):
                        $class = $index === 0 ? 'photo large' : 'photo';
                ?>
                    <div class="<?php echo $class; ?>">
                        <img src="./images/<?php echo $img; ?>.jpg" alt="Image <?php echo $index + 1; ?>" />
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Feedback Section -->
    <?php
        $sql = "SELECT name, feedback_text, rating FROM feeback LIMIT 2";
        $result = $conn->query($sql);
    ?>
    <section class="section7">
        <p class="styled-heading">Clients feedback</p>
        <div class="cards-container display-flex margin-left50 margin-top40">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="rating-card">
                    <p class="feedback-text"><?php echo htmlspecialchars($row['feedback_text']); ?></p>
                    <p class="name">--- <?php echo htmlspecialchars($row['name']); ?></p>
                    <p class="stars justify-centre">
                        <?php for ($i = 0; $i < $row['rating']; $i++): ?>
                            <i class="fa-regular fa-star"></i>
                        <?php endfor; ?>
                    </p>
                </div>
            <?php endwhile; $conn->close(); ?>
            
            <form id="feedbackForm">
                <input type="text" name="name" placeholder="Your Name">
                <input type="text" name="email" placeholder="Email" required />
                <input type="number" name="rating" placeholder="Rating out of 5" required />
                <textarea name="feedbackText" placeholder="Give us your honest feedback about our services" required></textarea>
                <button type="submit" class="">Submit</button>
                <div id="responseMessage"></div>
            </form>
        </div>
    </section>

    <?php include './includes/footer.php' ?>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="./scripts/swiper.js"></script>
    <script src="./scripts/index.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    // Get the referrer URL (previous page)
    const referrer = document.referrer;
    // Get the current page's origin (e.g., http://localhost/orlmahomes)
    const currentOrigin = window.location.origin;

    // Check if there's a referrer and it's not from the same origin
    if (referrer && !referrer.startsWith(currentOrigin)) {
        // Force a cache-free reload with a timestamp query parameter
        const timestamp = Date.now();
        const newUrl = `${window.location.pathname}?t=${timestamp}${window.location.hash}`;
        window.location.assign(newUrl);
    }
});
    </script>
</body>
</html>