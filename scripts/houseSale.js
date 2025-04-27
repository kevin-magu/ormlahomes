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

// ---- Category Select & Dynamic Subcategory Display ----
document.addEventListener("DOMContentLoaded", function () {
  const mainCategory = document.getElementById("mainCategory");
  const subcategories = document.querySelectorAll(".subcategory");
  const allInputs = document.querySelectorAll(".property-input");

  mainCategory.addEventListener("change", function () {
      const selected = mainCategory.value;

      // Hide all subcategories first
      subcategories.forEach(select => {
          select.style.display = "none";
      });

      // Show the one matching the selected category
      if (selected) {
          const matchingSelect = document.getElementById(selected + "Options");
          if (matchingSelect) {
              matchingSelect.style.display = "block";
          }
      }

      // Handle hiding/showing inputs depending on category
      if (selected === "lands") {
          allInputs.forEach(input => {
              const fieldsToHide = ["No of bedrooms", "No of bathrooms", "Amenities eg Swimming pool, Gym, Clubhouse e.t.c"];
              const placeholder = input.getAttribute("placeholder");

              if (fieldsToHide.includes(placeholder)) {
                  input.style.display = "none";
              } else {
                  input.style.display = "block";
              }
          });
      } else {
          // For other categories, show everything
          allInputs.forEach(input => {
              input.style.display = "block";
          });
      }
  });
});
