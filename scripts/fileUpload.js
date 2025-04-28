document.addEventListener("DOMContentLoaded", function() {
    // ---- File Upload & Preview ----
    const dropZone = document.getElementById("dropZone");
    const fileInput = document.getElementById("fileInput");
    const preview = document.getElementById("preview");
  
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

    const form = document.getElementById('sellForm');
    const mainCategory = document.getElementById('mainCategory');
    const subcategories = document.querySelectorAll('.subcategory');
    const charCounter = document.getElementById('charCounter');
    const descriptionInput = document.getElementById('propertyDescription');
  
    // Character counter for description
    descriptionInput.addEventListener('input', function() {
      charCounter.textContent = `${descriptionInput.value.length} / 700`;
    });
  
    // Show relevant subcategories based on main category selection
    mainCategory.addEventListener('change', function() {
      subcategories.forEach(select => select.style.display = 'none');
      const selectedSubcategory = document.getElementById(mainCategory.value + 'Options');
      if (selectedSubcategory) {
        selectedSubcategory.style.display = 'block';
      }
    });
  
    // ---- FIRST: Validate if user is logged in ----
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
      // else: token is valid, allow staying on page normally
    })
    .catch(error => {
      console.error('Error checking login:', error);
      alert('Login validation failed. Please try again.');
      window.location.href = './login.php';
    });
  
  });
  