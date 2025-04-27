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
        <div class="form-container display-flex justify-centre ">
            <form upload>
                <div class="form-group drop-zone margin-top50 display-flex justify-centre" id="dropZone">
                    <span>Drag & drop images of your property here or click to select. (max 5MB each)</span>
                    <input type="file" name="images[]" id="fileInput" accept="image/*" multiple required>
                </div>
                <div id="preview" class="preview-area"></div>
                <input type="text" name="" placeholder="Category">
                <input type="text" name="" placeholder="Property Type">
                <input type="text" name="" placeholder="Location">
                <input type="text" name="" placeholder="Google map link">
                <input type="text" name="" placeholder="Cost in Kes">
                <input type="text" name="" placeholder="Mortgage rate if applicable">
                <input type="text" name="" placeholder="No of bedrooms">
                <input type="text" name="" placeholder="No of bathrooms">
                <input type="text" name="" placeholder="Amenities eg Swimming pool, Gym, Clubhouse e.t.c">
                <input type="text" name="" placeholder="Nearby accessibilities like schools">
                <textarea name="" id="" placeholder="A short description of the house"></textarea>
                <div class="button-container display-flex">
                    <button>Upload</button>
                    <a href="">Report a problem</a>
                    <a href="">Request help</a>
                </div>
            </form>
        </div>

    <?php include './includes/footer.php' ?>
    <script src="./scripts/houseSale.js"></script>
</body>
</html>