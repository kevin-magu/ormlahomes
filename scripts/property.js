function showGallery() {
    document.getElementById("fullGallery").style.display = "flex";
    window.scrollTo(0, document.getElementById("fullGallery").offsetTop);
}

function hideGallery() {
    document.getElementById("fullGallery").style.display = "none";
}