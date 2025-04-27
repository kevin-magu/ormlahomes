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
