<?php 
include 'includes/connection.php';
include 'includes/navbar.php';

ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Property</title>
  <link rel="stylesheet" href="./styles/commonStyles.css">
  <link rel="stylesheet" href="./styles/upload.css">
</head>
<body>
  <div class="form-container">
    <h2>Add New Property</h2>
    
    <!-- Error messages -->
    <div id="error-messages" style="color: red;"></div>
    
    <!-- Property upload form -->
    <form id="uploadForm" action="./upload-handler" method="POST" enctype="multipart/form-data">
      
      <!-- Property Type -->
      <div class="form-group">
        <label for="propertyType">Property Type:</label>
        <select name="propertyType" id="propertyType" required>
          <option value="">Select Property Type</option>
          <option value="Apartment">Apartment</option>
          <option value="Condo">Condo</option>
          <option value="Townhouse">Townhouse</option>
        </select>
      </div>

      <!-- Listing Type -->
      <div class="form-group">
        <label for="listingType">Listing Type:</label>
        <select name="listingType" id="listingType" required>
          <option value="">Select Listing Type</option>
          <option value="Rental">Rental</option>
          <option value="Sale">Sale</option>
        </select>
      </div>

      <!-- Price -->
      <div class="form-group">
        <input type="text" name="price" placeholder="Price (e.g., Ksh 12,000,000)" required>
      </div>

      <!-- Description -->
      <div class="form-group">
        <textarea name="description" placeholder="Property Description" required></textarea>
      </div>

      <!-- Image Upload -->
      <div class="form-group drop-zone" id="dropZone">
        <span>Drag & drop images here or click to select (max 5MB each)</span>
        <input type="file" name="images[]" id="fileInput" accept="image/*" multiple required>
      </div>

      <!-- Image Preview -->
      <div id="preview" class="preview-area"></div>

      <!-- Submit Button -->
      <button type="submit">Upload Property</button>
    </form>
  </div>

  <!-- JavaScript for handling file uploads (e.g., previewing images) -->
  <script src="./upload.js"></script>
</body>
</html>
