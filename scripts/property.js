function showGallery() {
    document.getElementById("fullGallery").style.display = "flex";
    window.scrollTo(0, document.getElementById("fullGallery").offsetTop);
}

function hideGallery() {
    document.getElementById("fullGallery").style.display = "none";
}

window.addEventListener('DOMContentLoaded', () => {
    hideGallery(); // Ensures gallery is hidden on reload

    const photoGridImages = document.querySelectorAll('.photo-grid img');

    photoGridImages.forEach(img => {
        img.addEventListener('click', () => {
            showGallery();
        });
    });
});
