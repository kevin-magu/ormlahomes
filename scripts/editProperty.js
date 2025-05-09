document.addEventListener('DOMContentLoaded', () => {
  const updateBtn = document.querySelector('button[type="submit"]');
  const fileInput = document.getElementById('fileInput');

  if (!updateBtn) return;

  updateBtn.addEventListener('click', function (event) {
    event.preventDefault();

    const propertyId = document.querySelector('input[name="property_id"]').value;
  
    const propertyData = {
      propertyId: propertyId,
      title: document.getElementById('title')?.value || '',
      listingType: document.getElementById('listingType')?.value || '',
      mainCategory: document.getElementById('mainCategory')?.value || '',
      subcategory: document.querySelector('select[name="subcategory"]')?.value || '',
      location: document.getElementById('location')?.value || '',
      mapLink: document.getElementById('mapLink')?.value || '',
      cost: document.getElementById('cost')?.value || '',
      rentPerMonth: document.getElementById('rentPerMonth')?.value || '',
      propertySize: document.getElementById('propertySize')?.value || '',
      bedrooms: document.getElementById('bedrooms')?.value || '',
      bathrooms: document.getElementById('bathrooms')?.value || '',
      garages: document.getElementById('garages')?.value || '',
      yearBuilt: document.getElementById('yearBuilt')?.value || '',
      condition: document.getElementById('propertyCondition')?.value || '',
      floor: document.getElementById('floor')?.value || '',
      amenities: document.getElementById('amenities')?.value || '',
      nearby: document.getElementById('nearby')?.value || '',
      propertyDescription: document.getElementById('propertyDescription')?.value || '',
      images: []
    };
    console.log(JSON.stringify(propertyData))

    const files = fileInput?.files || [];
    const imagePromises = [];

    for (const file of files) {
      const reader = new FileReader();
      const promise = new Promise((resolve, reject) => {
        reader.onload = e => resolve(e.target.result);
        reader.onerror = reject;
      });
      reader.readAsDataURL(file);
      imagePromises.push(promise);
    }

    Promise.all(imagePromises)
      .then(base64Images => {
        propertyData.images = base64Images;

        return fetch('./editPropertyProcessing', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(propertyData)
        });
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showResponseMessage('Property updated successfully!');
        } else {
          showResponseMessage(data.message || 'Update failed.');
        }
      })
      .catch((error) => {
        showResponseMessage('Network error occurred. Try again.');
        console.error(error)
      });
  });
});
