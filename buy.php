<?php include './includes/connection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css" />
    <link rel="stylesheet" href="./styles/buy.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy </title>
</head>
<body>

<?php include './includes/navbar.php' ?>
    <div class="filter-container justify-centre margin-top30 dipslay-flex">
        <div class="p-filter-container disp justify-centre dipslay-flex">
            <p>Apartment</p>
            <p>Condo</p>
            <p>Bungalow</p>
            <p>Maisonette</p>
            <p>Townhouse</p>
            <p>Studio Apartment</p>
        </div>
        <select name="propertyPrice" id="propertyPrice" required>
          <option value="">property price</option>
          <option value="below1m">Below Ksh 1M </option>
          <option value="1m-5m">Ksh 1M - 5M</option>
          <option value="5m-10m">Ksh 5M - 10M</option>
          <option value="10m-15m">Ksh 10M - 15M</option>
          <option value="15m-20m">Ksh 15M - 20M</option>
          <option value="above">Above Ksh 20M</option>
        </select>
        <button>Apply Filter</button>
    </div>
    <div class="property-cards-container justify-centre margin-top30">

        <?php
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

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
                            <p>Ksh <?php echo htmlspecialchars($property['price'], ENT_QUOTES, 'UTF-8'); ?><?php echo $isRental ? ' /month' : ''; ?></p>
                            <p><i class="fa-solid fa-location-dot"></i>
                                <?php echo htmlspecialchars($property['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </a>
                </div>
                <?php
            }

            function renderProperties($conn, $listingType, $isRental = false) {
                $query = "SELECT * FROM properties WHERE listing_type = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $listingType);
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
        <?php renderProperties($conn, 'sale'); ?>
    </div>
    
<?php include './includes/footer.php' ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="./scripts/swiper.js"></script>
<script src="./scripts/index.js"></script>
</body>
</html>