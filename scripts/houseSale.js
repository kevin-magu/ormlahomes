document.addEventListener("DOMContentLoaded", function() {
  // ---- File Upload & Preview ----
  const dropZone = document.getElementById("dropZone");
  const fileInput = document.getElementById("fileInput");
  const preview = document.getElementById("preview");
  const submitBtn = document.getElementById("submitSellBtn"); // Submit button

  dropZone.addEventListener("click", () => fileInput.click());

  fileInput.addEventListener("change", updatePreview);

  dropZone.addEventListener("dragover", e => {
    e.preventDefault();
    dropZone.style.backgroundColor = "#e0f0ff";
  });

  dropZone.addEventListener("dragleave", () => {
    dropZone.style.backgroundColor = "#fafafa";
  });

  dropZone.addEventListener("drop", e => {
    e.preventDefault();
    fileInput.files = e.dataTransfer.files;
    updatePreview();
    dropZone.style.backgroundColor = "#fafafa";
  });

  function updatePreview() {
    preview.innerHTML = "";
    const files = fileInput.files;
    for (const file of files) {
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.createElement("img");
        img.src = e.target.result;
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  }

  // ---- Validate if user is logged in first ----
  const token = localStorage.getItem('token');

  if (!token) {
    alert('Authentication required. Please login.');
    window.location.href = './login.php';
    return;
  }

  fetch('./validate_token.php', {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + token
    }
  })
  .then(response => response.json())
  .then(data => {
    if (!data.success) {
      alert('Session expired. Please login again.');
      window.location.href = './login.php';
    }
    // Token valid
  })
  .catch(error => {
    console.error('Error checking login:', error);
    alert('Login validation failed. Please try again.');
    window.location.href = './login.php';
  });

  // ---- Character Counter for Property Description ----
  const descriptionInput = document.getElementById('propertyDescription');
  const charCounter = document.getElementById('charCounter');

  if (descriptionInput && charCounter) {
    descriptionInput.addEventListener('input', function () {
      charCounter.textContent = `${descriptionInput.value.length} / 700`;
    });
  } else {
    console.warn('Character counter elements not found!');
  }

  // ---- Category and Subcategory Selection ----
  const categorySelect = document.getElementById('mainCategory');
  const residentialOptions = document.getElementById('residentialOptions');
  const commercialOptions = document.getElementById('commercialOptions');
  const industrialOptions = document.getElementById('industrialOptions');
  const landsOptions = document.getElementById('landsOptions');

  // Initially hide all subcategory options
  function hideAllSubcategories() {
    residentialOptions.style.display = 'none';
    commercialOptions.style.display = 'none';
    industrialOptions.style.display = 'none';
    landsOptions.style.display = 'none';
  }

  // Show subcategory options based on category selection
  categorySelect.addEventListener('change', function () {
    hideAllSubcategories(); // Hide all options first
    const selectedCategory = categorySelect.value;
    
    switch (selectedCategory) {
      case 'residential':
        residentialOptions.style.display = 'block';
        break;
      case 'commercial':
        commercialOptions.style.display = 'block';
        break;
      case 'industrial':
        industrialOptions.style.display = 'block';
        break;
      case 'lands':
        landsOptions.style.display = 'block';
        break;
      default:
        // Do nothing if no category is selected or other case
        break;
    }
  });

  // ---- Upload Property Listing ----
  if (submitBtn) {
    submitBtn.addEventListener('click', function () {
      const propertyData = {
        listingType: document.getElementById('listingType').value,
        mainCategory: document.getElementById('mainCategory').value,
        subcategory: document.getElementById('subcategory') ? document.getElementById('subcategory').value : '',
        location: document.getElementById('location').value,
        mapLink: document.getElementById('mapLink').value,
        cost: document.getElementById('cost').value,
        mortgage: document.getElementById('mortgage').value,
        bedrooms: document.getElementById('bedrooms').value,
        bathrooms: document.getElementById('bathrooms').value,
        amenities: document.getElementById('amenities').value,
        nearby: document.getElementById('nearby').value,
        propertyDescription: document.getElementById('propertyDescription').value,
        propertySize: document.getElementById('propertySize').value,
        images: []
      };

      const files = fileInput.files;
      const promises = [];

      for (const file of files) {
        const reader = new FileReader();
        const promise = new Promise((resolve, reject) => {
          reader.onload = e => resolve(e.target.result);
          reader.onerror = reject;
        });
        reader.readAsDataURL(file);
        promises.push(promise);
      }

      Promise.all(promises)
        .then(base64Images => {
          propertyData.images = base64Images;

          return fetch("./sellProcessing.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(propertyData)
          });
        })
        .then(res => res.text())
        .then(text => {
          try {
            const data = JSON.parse(text);
            if (data.success) {
              alert('Property successfully listed!');
              window.location.reload();
            } else {
              alert('Error: ' + (data.message || 'Unknown error'));
            }
          } catch (error) {
            console.error('Invalid JSON response:', text);
            alert('Server error. See console.');
          }
        })
        .catch(error => {
          console.error('Error submitting property:', error);
          alert('Network error. Please try again.');
        });
    });
  } else {
    console.error('Submit button not found!');
  }
});
