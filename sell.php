<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css">
    <link rel="stylesheet" href="./styles/upload.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell</title>
</head>
<body>
    <?php include './includes/navbar.php'; ?>

    <p class="justify-centre all-pages-title margin-top30">We sell your property on your behalf.</p>
    <div id="userResponse" class="response-box hidden"></div>
    <div class="form-container display-flex justify-centre">
        <form id="sellForm" method="POST" action="./sellProcessing.php" enctype="multipart/form-data">
            <!-- File Upload -->
            <div class="form-group drop-zone margin-top50 display-flex justify-centre" id="dropZone">
                <span>Drag & drop images of your property here or click to select (max 5MB each).</span>
                <input type="file" name="images[]" id="fileInput" accept="image/*" multiple required>
            </div>

            <div id="preview" class="preview-area"></div>
            <input type="text" name="title" id="title" class="property-input" placeholder="Property Title (e.g. Modern 3-Bedroom in Kilimani)" required>
            <!-- Category and Subcategory Selection -->
            <div class="select-container display-flex">
                <select id="listingType" name="listingType" class="listingtype" required>
                    <option value="">Listing Type</option>
                    <option value="For sale">For sale</option>
                    <option value="Rental">Rental</option>
                </select>

                <select id="mainCategory" name="mainCategory" required>
                    <option value="">Category</option>
                    <option value="residential">Residential</option>
                    <option value="commercial">Commercial</option>
                    <option value="industrial">Industrial</option>
                    <option value="lands">Lands</option>
                </select>

                <!-- Subcategory Selects -->
                <select id="residentialOptions" name="subcategory" class="subcategory" style="display:none;">
                    <option value="Apartment">Apartment</option>
                    <option value="Condo">Condo</option>
                    <option value="Duplex">Duplex</option>
                    <option value="Vacation Home">Vacation Home</option>
                    <option value="Townhouse">Townhouse</option>
                </select>

                <select id="commercialOptions" name="subcategory" class="subcategory" style="display:none;">
                    <option value="office_space">Office Space</option>
                    <option value="retail_units">Retail Units</option>
                    <option value="malls">Malls</option>
                    <option value="restaurants_hotels">Restaurants & Hotels</option>
                    <option value="mixed_use">Mixed Use</option>
                </select>

                <select id="industrialOptions" name="subcategory" class="subcategory" style="display:none;">
                    <option value="warehouse">Warehouse</option>
                    <option value="factories">Factories</option>
                    <option value="manufacturing_plants">Manufacturing Plants</option>
                    <option value="distribution_centers">Distribution Centers</option>
                    <option value="storage_facilities">Storage Facilities</option>
                </select>

                <select id="landsOptions" name="subcategory" class="subcategory" style="display:none;">
                    <option value="vacant_lot">Vacant Lot</option>
                    <option value="agricultural_land">Agricultural Land</option>
                    <option value="development_land">Development Land</option>
                </select>
            </div>

            <!-- Property Details -->
            <!-- Property Details -->
            <input type="text" name="location" id="location" class="property-input" placeholder="Location (e.g. Kilimani, Nairobi)" required>
            <input type="text" name="mapLink" id="mapLink" class="property-input" placeholder="Google Map Link (Optional)">
            <input type="text" name="cost" id="cost" class="property-input" placeholder="Sale Price in KES" required>
            <input type="text" name="rentPerMonth" id="rentPerMonth" class="property-input" placeholder="Rent per Month in KES" style="display: none;">
            <input type="text" name="propertySize" id="propertySize" class="property-input" placeholder="Property Size (e.g. 1200 sqft)" required>

            <input type="text" name="bedrooms" id="bedrooms" class="property-input" placeholder="Number of Bedrooms" min="0" required>
            <input type="text" name="bathrooms" id="bathrooms" class="property-input" placeholder="Number of Bathrooms" min="0" required>
            <input type="text" name="garages" id="garages" class="property-input" placeholder="Number of Parking Spaces" min="0">

            <input type="text" name="yearBuilt" id="yearBuilt" class="property-input" placeholder="Year Built (e.g. 2019)">
            <input type="text" name="condition" id="propertyCondition" class="property-input" placeholder="Condition (e.g. New, Renovated)">
            <input type="text" name="floor" id="floor" class="property-input" placeholder="Floor Level (if applicable)">
            <input type="text" name="amenities" id="amenities" class="property-input" placeholder="Amenities (e.g. Pool, Gym)">
            <input type="text" name="nearby" id="nearby" class="property-input" placeholder="Nearby (e.g. Schools, Hospitals)">


            <!-- Property Description -->
            <textarea name="propertyDescription" id="propertyDescription" class="property-input" placeholder="Short Description of the Property (max 700 characters)" maxlength="700" required></textarea>
            <div id="charCounter">0 / 700</div>

            <!-- Submit Button -->
            <div class="button-container display-flex margin-top50">
                <button type="submit" id="submitSellBtn">Upload</button>
                <a href="#">Report a Problem</a>
                <a href="#">Request Help</a>
            </div>
        </form>
    </div>

    <?php include './includes/footer.php'; ?>

    <script src="./scripts/houseSale.js"></script>
</body>
</html>
