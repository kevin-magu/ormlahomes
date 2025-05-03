// Create a reusable function to initialize a Swiper instance
function initializeSwiper(swiperContainer) {
  if (!swiperContainer) return;

  const nextButton = swiperContainer.querySelector('.swiper-button-next');
  const prevButton = swiperContainer.querySelector('.swiper-button-prev');
  const paginationEl = swiperContainer.querySelector('.swiper-pagination');

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
      el: paginationEl,
      clickable: true,
    },
    on: {
      slideChange() {
        updateButtonVisibility(this);
      },
      reachEnd() {
        updateButtonVisibility(this);
      },
      reachBeginning() {
        updateButtonVisibility(this);
      },
    },
  });

  function updateButtonVisibility(swiperInstance) {
    if (prevButton) {
      prevButton.style.display = swiperInstance.isBeginning ? 'none' : 'flex';
    }
    if (nextButton) {
      nextButton.style.display = swiperInstance.isEnd ? 'none' : 'flex';
    }
  }

  updateButtonVisibility(swiper);
}

// Define the reinit function globally so other scripts can use it
function reinitializeAllSwipers() {
  const allSwipers = document.querySelectorAll('.mySwiper, .feedback-swiper');
  allSwipers.forEach(swiperContainer => initializeSwiper(swiperContainer));
}

// Prevent the heart click from affecting the <a> tag
function preventHeartIconLinkBehavior(event) {
  event.stopPropagation();
  event.preventDefault();
}

function updateFavoriteIcon(propertyId, iconElement) {
  fetch(`./favorites?property_id=${propertyId}`, {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' }
  })
    .then(res => res.json())
    .then(data => {
      if (data.favorited) {
        iconElement.classList.remove('fa-regular');
        iconElement.classList.add('fa-solid');
        iconElement.classList.add('favorited');
      } else {
        iconElement.classList.remove('fa-solid');
        iconElement.classList.add('fa-regular');
        iconElement.classList.remove('favorited');
      }
    })
    .catch(error => console.error('Error updating favorite icon:', error));
}

function toggleFavorite(propertyId, iconElement) {
  const action = iconElement.classList.contains('fa-solid') ? 'remove' : 'add';

  fetch('./favorites', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ property_id: propertyId, action })
  })
    .then(res => res.json())
    .then(data => {
      if (data.favorited) {
        iconElement.classList.remove('fa-regular');
        iconElement.classList.add('fa-solid');
        iconElement.classList.add('favorited');
      } else {
        iconElement.classList.remove('fa-solid');
        iconElement.classList.add('fa-regular');
        iconElement.classList.remove('favorited');
      }
    })
    .catch(error => console.error('Error toggling favorite:', error));
}

// Initialize heart icon event listeners
function reinitializeHeartIcons() {
  const heartIcons = document.querySelectorAll('.heart-icon');
  console.log('Heart icons initialized:', heartIcons.length);

  heartIcons.forEach(icon => {
    // Remove existing listeners to prevent duplicates
    const newIcon = icon.cloneNode(true);
    icon.parentNode.replaceChild(newIcon, icon);

    // Update favorite status
    const propertyId = newIcon.getAttribute('data-property-id');
    if (propertyId) {
      updateFavoriteIcon(propertyId, newIcon);
    }

    // Add click listener
    newIcon.addEventListener('click', (event) => {
      preventHeartIconLinkBehavior(event);

      const propId = newIcon.getAttribute('data-property-id');
      if (propId) {
        toggleFavorite(propId, newIcon);
      }
    });
  });
}

// Expose globally
window.reinitializeAllSwipers = reinitializeAllSwipers;
window.reinitializeHeartIcons = reinitializeHeartIcons;

// Run on initial load
document.addEventListener("DOMContentLoaded", () => {
  reinitializeAllSwipers();
  reinitializeHeartIcons();
});