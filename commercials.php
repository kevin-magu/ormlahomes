<?php include './includes/connection.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Properties</title>
    <link rel="stylesheet" href="./styles/commonStyles.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/buy.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body>
<div class="sticky-top">
<?php include './includes/navbar.php';?>
    <p class="justify-centre all-pages-title margin-top30">Find the perfect space to setup your business.</p>
    <div class="filter-button-container justify-centre margin-top30"><button><i class="fa-solid fa-filter"></i> Filters</button></div>
<div class="filter-container justify-centre margin-top30 dipslay-flex">
    <div class="p-filter-container disp justify-centre dipslay-flex" id="propertyTypes">
    <i class="fa-solid fa-rectangle-xmark" style="color: #ff0000;"></i>
        <p data-type="all">All</p>
        <p data-type="Office Space">Office Space</p>
        <p data-type="retail units">Retail units</p>
        <p data-type="malls">Malls</p>
        <p data-type="restaurant & hotel ">Restaurants & Hotels</p>
        <p data-type="mixed use">Mixed use</p>

        <select name="listingType" id="listingType">
            <option value="">All categories</option>
            <option value="For sale">For sale</option>
            <option value="rental">Rentals</option>
        </select>

        <select name="propertyPrice" id="propertyPrice">
            <option value="">All prices</option>
            <option value="below1m">Below Ksh 1M</option>
            <option value="1m-5m">Ksh 1M - 5M</option>
            <option value="5m-10m">Ksh 5M - 10M</option>
            <option value="10m-15m">Ksh 10M - 15M</option>
            <option value="15m-20m">Ksh 15M - 20M</option>
            <option value="above">Above Ksh 20M</option>
        </select>
        <select name="propertyLocation" id="propertyLocation">
            <option value="">All locations</option>
            <option value="Syokimau">Syokimau</option>
            <option value="Nairobi">Nairobi</option>
            <option value="Ruiru">Ruiru</option>
            <option value="Kilimani">Kilimani</option>
            <option value="Kileleshua">Kileleshua</option>
            <option value="Kitengela">Kitengela</option>
            <option value="Ngong">Ngong</option>
        </select>
        <button id="applyFilter">Apply Filter</button>
    </div> 
</div>
</div>
<div id="userResponse" class="response-box hidden"></div>

<div class="property-cards-wrapper justify-centre" id="propertyResults">
    <?php
    function renderImageSlider($images) {
        ob_start(); ?>
        <section class="justify-centre">
                    <div class="swiper mySwiper" style="position: relative;">
                    <!-- Heart icon moved outside swiper-wrapper -->
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
        <?php return ob_get_clean();
    }

    function renderPropertyCard($property, $conn) {
        $imageQuery = "SELECT * FROM property_images WHERE property_id = ?";
        $imgStmt = $conn->prepare($imageQuery);
        $imgStmt->bind_param("i", $property['id']);
        $imgStmt->execute();
        $images = $imgStmt->get_result();
    
        ob_start(); ?>
        
        <?php $token = base64_encode("property_" . $property['id']); ?>
        <a href="property?ref=<?= urlencode($token) ?>"> 
        <div class="property-card">
        <i class="fa-regular fa-heart heart-icon" data-property-id="<?php echo htmlspecialchars((int) $property['id'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                <?php $token = base64_encode("property_" . $property['id']); ?>
                    <?php echo renderImageSlider($images); ?>
                    <h3><?= htmlspecialchars($property['property_type']) ?></h3>
                    <div class="listing-type"><?= htmlspecialchars($property['listing_type']) ?></div>
                    <div class="display-flex homesize">
                        <p class="card-square"></p>
                        <p><?= htmlspecialchars($property['propertySize']) ?> SQFT</p>    
                    </div>
                    
                    <p>Ksh <?php echo number_format((int)$property['price'])?></p>
                    <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($property['location']) ?></p>
            
            </div>
            </a>
        
        <?php return ob_get_clean();
    }
    

    $stmt = $conn->prepare("SELECT * FROM properties WHERE broad_category = 'commercial'");
$stmt->execute();
$result = $stmt->get_result();

while ($property = $result->fetch_assoc()) {
    echo renderPropertyCard($property, $conn);
}


    $conn = null;
    ?>
</div>


<?php include './includes/footer.php' ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="./scripts/swiper.js"></script>
<script src="./scripts/commercials.js"></script>
</body>
</html>
