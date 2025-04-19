<?php
include './includes/connection.php';
include './includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Properties</title>
    <link rel="stylesheet" href="./styles/commonStyles.css" />
    <link rel="stylesheet" href="./styles/buy.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://kit.fontawesome.com/6a82c0f93a.js" crossorigin="anonymous"></script>
    <style>
        .p-filter-container p.selected {
            background-color: #222;
            color: #fff;
            border-radius: 4px;
            padding: 4px 8px;
        }
    </style>
</head>
<body>
<div class="page-container">
<div class="filter-container justify-centre margin-top30 dipslay-flex">
    <div class="p-filter-container disp justify-centre dipslay-flex" id="propertyTypes">
        <p data-type="all">All</p>
        <p data-type="Apartment">Apartment</p>
        <p data-type="Condo">Condo</p>
        <p data-type="Bungalow">Bungalow</p>
        <p data-type="Maisonette">Maisonette</p>
        <p data-type="Townhouse">Townhouse</p>
        <p data-type="Studio Apartment">Studio Apartment</p>
    </div>
    <select name="propertyPrice" id="propertyPrice">
        <option value="">property price</option>
        <option value="below1m">Below Ksh 1M</option>
        <option value="1m-5m">Ksh 1M - 5M</option>
        <option value="5m-10m">Ksh 5M - 10M</option>
        <option value="10m-15m">Ksh 10M - 15M</option>
        <option value="15m-20m">Ksh 15M - 20M</option>
        <option value="above">Above Ksh 20M</option>
    </select>
    <button id="applyFilter">Apply Filter</button>
</div>

<div class="property-cards-container justify-centre margin-top30" id="propertyResults">
    <?php
    function renderImageSlider($images) {
        ob_start(); ?>
        <section class="justify-centre">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php while ($image = $images->fetch_assoc()): ?>
                        <div class="swiper-slide">
                            <div class="image-slide"
                                 style="background-image: url('<?php echo htmlspecialchars($image['image_url']); ?>');">
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
        <div class="cards-container">
            <a href="#">
                <div class="property-card">
                    <?php echo renderImageSlider($images); ?>
                    <h3><?= htmlspecialchars($property['property_type']) ?></h3>
                    <div class="display-flex homesize">
                        <p class="card-square"></p>
                        <p><?= htmlspecialchars($property['homeSize']) ?> SQFT</p>
                    </div>
                    <p>Ksh <?= htmlspecialchars($property['price']) ?></p>
                    <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($property['description']) ?></p>
                </div>
            </a>
        </div>
        <?php return ob_get_clean();
    }

    $stmt = $conn->prepare("SELECT * FROM properties WHERE listing_type = 'sale'");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($property = $result->fetch_assoc()) {
        echo renderPropertyCard($property, $conn);
    }

    $conn = null;
    ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="./scripts/swiper.js"></script>
<script src="./scripts/buy.js"></script>
<?php include './includes/footer.php' ?>
</div>
</body>
</html>
