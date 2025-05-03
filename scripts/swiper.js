

// Create a reusable function to initialize a Swiper instance
function initializeSwiper(swiperContainer) {
  const nextButton = swiperContainer.querySelector('.swiper-button-next');
  const prevButton = swiperContainer.querySelector('.swiper-button-prev');

  const isFeedbackSwiper = swiperContainer.classList.contains('feedback-swiper');

  const swiper = new Swiper(swiperContainer, {
    loop: true,
    slidesPerView: isFeedbackSwiper ? 2 : 1,
    spaceBetween: isFeedbackSwiper ? 2 : 20,
    navigation: {
      nextEl: nextButton,
      prevEl: prevButton,
    },
    pagination: {
      el: swiperContainer.querySelector('.swiper-pagination'),
      clickable: true,
    },
    on: {
      slideChange: function () {
        updateButtonVisibility(this);
      },
      reachEnd: function () {
        updateButtonVisibility(this);
      },
      reachBeginning: function () {
        updateButtonVisibility(this);
      },
    },
  });

  function updateButtonVisibility(swiperInstance) {
    if (swiperInstance.isBeginning) {
      prevButton.style.display = 'none';
    } else {
      prevButton.style.display = 'flex';
    }

    if (swiperInstance.isEnd) {
      nextButton.style.display = 'none';
    } else {
      nextButton.style.display = 'flex';
    }
  }

  updateButtonVisibility(swiper);
}

// Define the reinit function globally so other scripts can use it
function reinitializeAllSwipers() {
  const allSwipers = document.querySelectorAll('.mySwiper, .feedback-swiper');
  allSwipers.forEach(swiperContainer => {
    initializeSwiper(swiperContainer);
  });
}

// Expose globally
window.reinitializeAllSwipers = reinitializeAllSwipers;

// Also run on initial load
document.addEventListener("DOMContentLoaded", () => {
  reinitializeAllSwipers();
});


document.addEventListener('DOMContentLoaded', () => {
  // Select all heart icons
  const heartIcons = document.querySelectorAll('.heart-icon');

  heartIcons.forEach(icon => {
      icon.addEventListener('click', (event) => {
          // Prevent the click from propagating to the parent <a> tag
          event.preventDefault();
          event.stopPropagation();

          // Get the property ID from the data attribute
          const propertyId = icon.getAttribute('data-property-id');

          // Toggle the heart icon's appearance (e.g., filled vs. unfilled)
          icon.classList.toggle('fa-regular');
          icon.classList.toggle('fa-solid');

          // Implement favorite functionality (e.g., send to server)
         // toggleFavorite(propertyId, icon);
      });
  });

});