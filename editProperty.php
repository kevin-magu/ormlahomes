<?php
include './includes/navbar.php';
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
            <span>Upload new images (optional)</span>
            <input type="file" name="images[]" id="fileInput" accept="image/*" multiple>
        </div>
        <div id="preview" class="preview-area"></div>

        <!-- Property Fields -->
        <input type="text" id="title" name="title" class="property-input" placeholder="Title" value="<?= $property['title'] ?>" required>
        
        <div class="select-container display-flex">
        <select id="listingType" name="listingType" required>
        <option value="">Listing Type</option>
        <option value="For Sale" <?= $property['listing_type'] === 'For sale' ? 'selected' : '' ?>>For Sale</option>
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

            <select name="subcategory" required>
                <?php
                $sub = $property['property_type'];
                $allSubs = ['Apartment','Condo','Duplex','Vacation Home','Townhouse','office_space','retail_units','malls','restaurants_hotels','mixed_use','warehouse','factories','manufacturing_plants','distribution_centers','storage_facilities','vacant_lot','agricultural_land','development_land'];
                foreach ($allSubs as $s) {
                    echo "<option value='$s'" . ($sub === $s ? ' selected' : '') . ">" . ucfirst(str_replace('_', ' ', $s)) . "</option>";
                }
                ?>
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
            <button id="deleteBtn" style="background-color: red; font-weight: bold;">Delete Property</button>
            <a href="#">Report a Problem</a>
            <a href="#">Request Help</a>
        </div>
    </form>
</div>

<?php include './includes/footer.php'; ?>

<script src="./scripts/houseSale.js"></script>
<script src="./scripts/deleteImages.js"></script>
<script src="./scripts/editProperty.js"></script>
</body>
</html>
