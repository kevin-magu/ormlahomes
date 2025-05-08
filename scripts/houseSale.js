document.addEventListener("DOMContentLoaded", function () {
  // ---- File Upload & Preview ----
  const dropZone = document.getElementById("dropZone");
  const fileInput = document.getElementById("fileInput");
  const preview = document.getElementById("preview");
  const submitBtn = document.getElementById("submitSellBtn");

  dropZone?.addEventListener("click", () => fileInput?.click());

  fileInput?.addEventListener("change", updatePreview);

  dropZone?.addEventListener("dragover", e => {
    e.preventDefault();
    dropZone.style.backgroundColor = "#e0f0ff";
  });

  dropZone?.addEventListener("dragleave", () => {
    dropZone.style.backgroundColor = "#fafafa";
  });

  dropZone?.addEventListener("drop", e => {
    e.preventDefault();
    fileInput.files = e.dataTransfer.files;
    updatePreview();
    dropZone.style.backgroundColor = "#fafafa";
  });

  function updatePreview() {
    if (!preview || !fileInput?.files) return;
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

  // ---- Validate if user is logged in ----
const token = localStorage.getItem('token');
if (!token) {
  showResponseMessage('You must be logged in to access this Feature', 2000); // Show message for 2 seconds
  setTimeout(() => {
    window.location.href = './login.php'; // Redirect to login page after 2 seconds
  }, 2000);
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
      showResponseMessage('Session expired. Please login again.', 2000); // Show message for 2 seconds
      setTimeout(() => {
        window.location.href = './login.php'; // Redirect to login page after 2 seconds
      }, 2000);
    }
  })
  .catch(() => {
    showResponseMessage('Login validation failed. Please try again.', 2000); // Show message for 2 seconds
    setTimeout(() => {
      window.location.href = './login.php'; // Redirect to login page after 2 seconds
    }, 2000);
  });


  // ---- Character Counter for Description ----
  const descriptionInput = document.getElementById('propertyDescription');
  const charCounter = document.getElementById('charCounter');

  if (descriptionInput && charCounter) {
    descriptionInput.addEventListener('input', function () {
      charCounter.textContent = `${descriptionInput.value.length} / 700`;
    });
  }

  // ---- Category & Subcategory Handling ----
const categorySelect = document.getElementById('mainCategory');
const residentialOptions = document.getElementById('residentialOptions');
const commercialOptions = document.getElementById('commercialOptions');
const industrialOptions = document.getElementById('industrialOptions');
const landsOptions = document.getElementById('landsOptions');

const listingTypeSelect = document.getElementById('listingType');
const costInput = document.getElementById('cost');
const rentPerMonthInput = document.getElementById('rentPerMonth');

// Function to hide all subcategories
function hideAllSubcategories() {
    residentialOptions.style.display = 'none';
    commercialOptions.style.display = 'none';
    industrialOptions.style.display = 'none';
    landsOptions.style.display = 'none';
}

// Function to show fields based on listing type
function handleListingType() {
    // Hide Rent per Month and Cost initially
    rentPerMonthInput.style.display = 'none';
    costInput.style.display = 'none';

    // Show fields based on listing type
    if (listingTypeSelect.value === 'For Sale') {
        costInput.style.display = 'block';  // Show cost for sale
    } else if (listingTypeSelect.value === 'Rental') {
        rentPerMonthInput.style.display = 'block';  // Show rent per month for rental
    }
}

// Set event listeners for category change
categorySelect?.addEventListener('change', function () {
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

// Set event listener for listing type change
listingTypeSelect?.addEventListener('change', handleListingType);

// Call the function once to set the initial state
handleListingType();

// ---- Submit Property Listing ----
if (submitBtn) {
  submitBtn.addEventListener('click', function (event) {
    // Prevent page reload
    event.preventDefault();

    const propertyData = {
      listingType: document.getElementById('listingType')?.value || '',
      mainCategory: document.getElementById('mainCategory')?.value || '',
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
      location: document.getElementById('location')?.value || '',
      mapLink: document.getElementById('mapLink')?.value || '',
      cost: document.getElementById('cost')?.value || '',
      rentPerMonth: document.getElementById('rentPerMonth')?.value || '',  // Added rent per month
      title: document.getElementById('title')?.value || '',  // Added title field
      propertyCondition: document.getElementById('propertyCondition')?.value || '',  // Added property condition
      yearBuilt: document.getElementById('yearBuilt')?.value || '',  // Added year built
      floor: document.getElementById('floor')?.value || '',  // Added floor
      bedrooms: document.getElementById('bedrooms')?.value || '',
      bathrooms: document.getElementById('bathrooms')?.value || '',
      garages: document.getElementById('garages')?.value || '',
      amenities: document.getElementById('amenities')?.value || '',
      nearby: document.getElementById('nearby')?.value || '',
      propertyDescription: document.getElementById('propertyDescription')?.value || '',
      propertySize: document.getElementById('propertySize')?.value || '',
      images: []
    };

    const files = fileInput?.files || [];
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

        return fetch("./sellProcessing", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(propertyData)
        });
      })
      .then(res => {
        if (res.redirected) {
          window.location.href = res.url;
        } else {
          return res.json().then(data => {
            if (data?.message) {
              showResponseMessage(data.message);
              // Clear the form fields after successful submission
            }
            if(data.status == 'success') {
              clearFormFields();
            }
          });
        }
      })
      .catch(() => {
        showResponseMessage('Network error. Please try again.');
      });
  });
}

// ---- Response Message Display ----
function showResponseMessage(message, duration = 6000) {
  const responseBox = document.getElementById('userResponse');
  if (!responseBox) return;
  responseBox.textContent = message;
  responseBox.classList.remove('hidden');
  responseBox.classList.add('show');

  setTimeout(() => {
    responseBox.classList.remove('show');
    responseBox.classList.add('hidden');
  }, duration);
}
window.showResponseMessage = showResponseMessage;

// ---- Clear Form Fields ----
function clearFormFields() {
  document.getElementById('listingType').value = '';
  document.getElementById('mainCategory').value = '';
  document.getElementById('location').value = '';
  document.getElementById('mapLink').value = '';
  document.getElementById('cost').value = '';
  document.getElementById('rentPerMonth').value = '';  // Clear rent per month
  document.getElementById('title').value = '';  // Clear title
  document.getElementById('propertyCondition').value = '';  // Clear property condition
  document.getElementById('yearBuilt').value = '';  // Clear year built
  document.getElementById('floor').value = '';  // Clear floor
  document.getElementById('bedrooms').value = '';
  document.getElementById('bathrooms').value = '';
  document.getElementById('garages').value = '';
  document.getElementById('amenities').value = '';
  document.getElementById('nearby').value = '';
  document.getElementById('propertyDescription').value = '';
  document.getElementById('propertySize').value = '';
  fileInput.value = '';  // Clear file input
  preview.innerHTML = '';  // Clear image preview
}

 
});
