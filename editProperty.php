<?php
include './includes/connection.php';

$propertyId = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM properties WHERE id = '$propertyId'");
$property = mysqli_fetch_assoc($result);

// Get all images for the property
$imageQuery = mysqli_query($conn, "SELECT * FROM property_images WHERE property_id = '$propertyId'");
$images = [];
while ($row = mysqli_fetch_assoc($imageQuery)) {
    $images[] = $row['image_url'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css">
    <link rel="stylesheet" href="./styles/upload.css">
    <link rel="stylesheet" href="./styles/edit-property.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property</title>
</head>
<body>
<?php include './includes/navbar.php'; ?>
<div id="userResponse" class="response-box hidden"></div>
<p class="justify-centre all-pages-title margin-top30">Edit your property listing</p>
<p style="display: none;" >Hello</p>
<p></p>
<div class="form-container display-flex justify-centre">
    <form id="editForm"  >
        <input type="hidden" name="property_id" value="<?= $property['id'] ?>">

        <div class="existing-images margin-top50">
                <?php
                $imageQuery = mysqli_query($conn, "SELECT * FROM property_images WHERE property_id = '$propertyId'");
                while ($row = mysqli_fetch_assoc($imageQuery)) {
                    $imageId = $row['id'];
                    $imageUrl = $row['image_url'];
                    echo '
                    <div class="image-wrapper" data-image-id="' . $imageId . '">
                        <i class="fa-solid fa-trash delete-image" data-id="' . $imageId . '" data-url="'. $imageUrl .'"></i>
                        <img src="' . htmlspecialchars($imageUrl) . '" width="150">  
                    </div>';
                }
                ?>
        </div>


        <!-- New Images Upload -->
        <div class="form-group drop-zone margin-top50 display-flex justify-centre" id="dropZone">
            <span>Drag & drop images of your property here or click to select (max 5MB each).</span>
            <input type="file" name="images[]" id="fileInput" accept="image/*" multiple>
        </div>
        <div id="preview" class="preview-area"></div>

        <!-- Property Fields -->
        <input type="text" id="title" name="title" class="property-input" placeholder="Title" value="<?= $property['title'] ?>" required>
        
        <div class="select-container display-flex">
        <select id="listingType" name="listingType" required>
        <option value="">Listing Type</option>
        <option value="For sale" <?= $property['listing_type'] === 'For sale' ? 'selected' : '' ?>>For sale</option>
        <option value="Rental" <?= $property['listing_type'] === 'Rental' ? 'selected' : '' ?>>Rental</option>
        </select>
        
        <select id="mainCategory" name="mainCategory" required>
    <option value="">Category</option>
    <?php
    $categories = ['residential', 'commercial', 'industrial', 'lands'];
    foreach ($categories as $cat) {
        echo "<option value='$cat'" . ($property['broad_category'] === $cat ? ' selected' : '') . ">" . ucfirst($cat) . "</option>";
    }
    ?>
</select>

<!-- Subcategory Selects (with PHP to preselect $property['listing_type']) -->
<select id="residentialOptions" name="subcategory" class="subcategory" style="display:none;">
    <option value="Apartment" <?= $property['listing_type'] === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
    <option value="Condo" <?= $property['listing_type'] === 'Condo' ? 'selected' : '' ?>>Condo</option>
    <option value="Duplex" <?= $property['listing_type'] === 'Duplex' ? 'selected' : '' ?>>Duplex</option>
    <option value="Vacation Home" <?= $property['listing_type'] === 'Vacation Home' ? 'selected' : '' ?>>Vacation Home</option>
    <option value="Townhouse" <?= $property['listing_type'] === 'Townhouse' ? 'selected' : '' ?>>Townhouse</option>
</select>

<select id="commercialOptions" name="subcategory" class="subcategory" style="display:none;">
    <option value="office_space" <?= $property['listing_type'] === 'office_space' ? 'selected' : '' ?>>Office Space</option>
    <option value="retail_units" <?= $property['listing_type'] === 'retail_units' ? 'selected' : '' ?>>Retail Units</option>
    <option value="malls" <?= $property['listing_type'] === 'malls' ? 'selected' : '' ?>>Malls</option>
    <option value="restaurants_hotels" <?= $property['listing_type'] === 'restaurants_hotels' ? 'selected' : '' ?>>Restaurants & Hotels</option>
    <option value="mixed_use" <?= $property['listing_type'] === 'mixed_use' ? 'selected' : '' ?>>Mixed Use</option>
</select>

<select id="industrialOptions" name="subcategory" class="subcategory" style="display:none;">
    <option value="warehouse" <?= $property['listing_type'] === 'warehouse' ? 'selected' : '' ?>>Warehouse</option>
    <option value="factories" <?= $property['listing_type'] === 'factories' ? 'selected' : '' ?>>Factories</option>
    <option value="manufacturing_plants" <?= $property['listing_type'] === 'manufacturing_plants' ? 'selected' : '' ?>>Manufacturing Plants</option>
    <option value="distribution_centers" <?= $property['listing_type'] === 'distribution_centers' ? 'selected' : '' ?>>Distribution Centers</option>
    <option value="storage_facilities" <?= $property['listing_type'] === 'storage_facilities' ? 'selected' : '' ?>>Storage Facilities</option>
</select>

<select id="landsOptions" name="subcategory" class="subcategory" style="display:none;">
    <option value="vacant_lot" <?= $property['listing_type'] === 'Vacant Lot' ? 'selected' : '' ?>>Vacant Lot</option>
    <option value="agricultural_land" <?= $property['listing_type'] === 'agricultural_land' ? 'selected' : '' ?>>Agricultural Land</option>
    <option value="development_land" <?= $property['listing_type'] === 'development_land' ? 'selected' : '' ?>>Development Land</option>
</select>

        </div>
        <input type="hidden" name="property_id" value="<?php echo  $propertyId ?>" />
        <input type="text" name="location" id="location" class="property-input" placeholder="Location" value="<?= $property['location'] ?>" required>
        <input type="text" name="mapLink" id="mapLink" class="property-input" placeholder="Google Map Link" value="<?= $property['map_link'] ?>">
        <input type="text" name="cost" id="cost" class="property-input" placeholder="Sale Price" value="<?= $property['price'] ?>" required>
        <input type="text" name="rentPerMonth"  id="rentPerMonth" class="property-input" placeholder="Rent per Month" value="<?= $property['rent_per_month'] ?>">
        <input type="text" name="propertySize" id="propertySize" class="property-input" placeholder="Property Size" value="<?= $property['propertySize'] ?>" required>

        <input type="text" name="bedrooms" id="bedrooms" class="property-input" placeholder="Bedrooms" value="<?= $property['bedrooms'] ?>" required>
        <input type="text" name="bathrooms" id="bathrooms" class="property-input" placeholder="Bathrooms" value="<?= $property['bathrooms'] ?>" required>
        <input type="text" name="garages" id="garages" class="property-input" placeholder="Parking Spaces" value="<?= $property['garage'] ?>">
        <input type="text" name="yearBuilt" id="yearBuilt" class="property-input" placeholder="Year Built" value="<?= $property['yearBuilt'] ?>">
        <input type="text" name="condition" id="propertyCondition" class="property-input" placeholder="Condition" value="<?= $property['property_condition'] ?>">
        <input type="text" name="floor" id="floor" class="property-input" placeholder="Floor Level" value="<?= $property['floor'] ?>">
        <input type="text" name="amenities" id="amenitie" class="property-input" placeholder="Amenities" value="<?= $property['accessibilities'] ?>">
        <input type="text" name="nearby" id="nearby" class="property-input" placeholder="Nearby" value="<?= $property['other_property_amenities'] ?>">

        <textarea name="propertyDescription" id="propertyDescription" class="property-input" maxlength="700" required><?= $property['description'] ?></textarea>
        <div id="charCounter"><?= strlen($property['description']) ?> / 700</div>

        <div class="button-container display-flex margin-top50">
            <button type="submit">Update Property</button>
            <button type id="deleteBtn" style="background-color: red; font-weight: bold;">Delete Property</button>
            <a href="#">Report a Problem</a>
            <a href="#">Request Help</a>
        </div>
    </form>
</div>

<?php include './includes/footer.php'; ?>

<script src="./scripts/houseSale.js"></script>
<script src="./scripts/deleteImages.js"></script>
<script src="./scripts/editProperty.js"></script>
<script src="./scripts/deleteProperty.js"></script>
</body>
</html>
