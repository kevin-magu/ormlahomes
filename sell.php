

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
    <?php include './includes/navbar.php' ?>

        <p class="justify-centre all-pages-title margin-top30">We sell your property on your behalf.</p>
        <div class="form-container display-flex justify-centre">
            <form >
                <div class="form-group drop-zone margin-top50 display-flex justify-centre" id="dropZone">
                    <span>Drag & drop images of your property here or click to select. (max 5MB each)</span>
                    <input type="file" name="images[]" id="fileInput" accept="image/*" multiple required>
                </div>

                <div id="preview" class="preview-area"></div>

                <div class="select-container display-flex">

                <select id="listingType" class="listingtype">
                    <option>For Sale</option>
                    <option>Rental</option>
                </select>

                <!-- Main Category Selector -->
                <select id="mainCategory">
                    <option value="residential">Residential</option>
                    <option value="commercial">Commercial</option>
                    <option value="industrial">Industrial</option>
                    <option value="lands">Lands</option>
                </select>
                
                <!-- Subcategory Selects -->
                <select id="residentialOptions" class="subcategory" style="display:none;">
                    <option>Apartment</option>
                    <option>Condo</option>
                    <option>Duplex</option>
                    <option>Vacation Home</option>
                    <option>Townhouse</option>
                </select>
                
                <select id="commercialOptions" class="subcategory" style="display:none;">
                    <option>Office Space</option>
                    <option>Retail Units</option>
                    <option>Malls</option>
                    <option>Restaurants & Hotels</option>
                    <option>Mixed use</option>
                </select>
                
                <select id="industrialOptions" class="subcategory" style="display:none;">
                    <option>Warehouse</option>
                    <option>Factories</option>
                    <option>Manufacturing plants</option>
                    <option>Distribution centers</option>
                    <option>Storage facilities</option>
                </select>
                
                <select id="landsOptions" class="subcategory" style="display:none;">
                    <option>Vacant lot</option>
                    <option>Agricultural land</option>
                    <option>Development land</option>
                </select>

                </div>

                <input type="text" class="property-input" placeholder="Location">
                <input type="text" class="property-input" placeholder="Google map link">
                <input type="text" class="property-input" placeholder="Cost in Kes">
                <input type="text" class="property-input" placeholder="Mortgage rate if applicable">
                <input type="text" class="property-input" placeholder="No of bedrooms">
                <input type="text" class="property-input" placeholder="No of bathrooms">
                <input type="text" class="property-input" placeholder="Amenities eg Swimming pool, Gym, Clubhouse e.t.c">
                <input type="text" class="property-input" placeholder="Nearby accessibilities like schools">
                <textarea id="propertyDescription" class="property-input" placeholder="A short description of the property" maxlength="700"></textarea>
                <div id="charCounter">0 / 700</div>

                <div class="button-container display-flex margin-top50">
                    <button>Upload</button>
                    <a href="">Report a problem</a>
                    <a href="">Request help</a>
                </div>
            </form>
        </div>

    <?php include './includes/footer.php' ?>
    <script>
            const token = localStorage.getItem('token');

            if (!token) {
                // If no token, redirect to login page
                window.location.href = './login.php';
            } else {
                // Send the token with the request to validate
                fetch('./validate_token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token // Send token as Authorization header
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // If validation fails, redirect to login page
                        window.location.href = '/login.php';
                    } else {
                        // Token is valid, show the Sell page content
                    }
                });
            }

    </script>
    <script src="./scripts/houseSale.js"></script>
</body>
</html>
