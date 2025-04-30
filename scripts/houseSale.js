document.addEventListener("DOMContentLoaded", function () {
  // ---- File Upload & Preview ----
  const dropZone = document.getElementById("dropZone");
  const fileInput = document.getElementById("fileInput");
  const preview = document.getElementById("preview");
  const submitBtn = document.getElementById("submitSellBtn");

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

  function hideAllSubcategories() {
    residentialOptions.style.display = 'none';
    commercialOptions.style.display = 'none';
    industrialOptions.style.display = 'none';
    landsOptions.style.display = 'none';
  }

  categorySelect.addEventListener('change', function () {
    hideAllSubcategories();
    switch (categorySelect.value) {
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
    }
  });

  // ---- Upload Property Listing ----
  if (submitBtn) {
    submitBtn.addEventListener('click', function () {
      const propertyData = {
        listingType: document.getElementById('listingType').value,
        mainCategory: document.getElementById('mainCategory').value,
        subcategory: (() => {
          const subcategorySelects = document.getElementsByClassName('subcategory');
          for (let i = 0; i < subcategorySelects.length; i++) {
            const select = subcategorySelects[i];
            if (select.style.display !== 'none') {
              return select.value;
            }
          }
          return '';
        })(),
        location: document.getElementById('location').value,
        mapLink: document.getElementById('mapLink').value,
        cost: document.getElementById('cost').value,
        mortgage: document.getElementById('mortgage').value,
        bedrooms: (document.getElementById('bedrooms').value),
        bathrooms: (document.getElementById('bathrooms').value),        
        garages: (document.getElementById('garages').value),
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

          console.log("Form Data Being Sent:", propertyData)

          return fetch("./sellProcessing", {
            method: "POST",
            headers: {
              "Content-Type": "application/json"
            },
            body: JSON.stringify(propertyData)
          });
        })
        .then(res => res.json()) // ✅ use .json() instead of .text()
        .then(data => {
          console.log(data)
          if (data.success) {
            alert('Property successfully listed!');
            window.location.reload();
          } else {
            alert('Error: ' + (data.message || 'Unknown error'));
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
