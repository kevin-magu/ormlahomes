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
    navigation: { nextEl: nextButton, prevEl: prevButton },
    pagination: { el: paginationEl, clickable: true },
    on: {
      slideChange() { updateButtonVisibility(this); },
      reachEnd() { updateButtonVisibility(this); },
      reachBeginning() { updateButtonVisibility(this); },
    },
  });

  function updateButtonVisibility(swiperInstance) {
    if (prevButton) prevButton.style.display = swiperInstance.isBeginning ? 'none' : 'flex';
    if (nextButton) nextButton.style.display = swiperInstance.isEnd ? 'none' : 'flex';
  }

  updateButtonVisibility(swiper);
}

function reinitializeAllSwipers() {
  document.querySelectorAll('.mySwiper, .feedback-swiper').forEach(initializeSwiper);
}

function preventHeartIconLinkBehavior(event) {
  event.preventDefault();
  event.stopPropagation();
}

function updateFavoriteIcon(propertyId, iconElement) {
  if (!propertyId || isNaN(propertyId) || propertyId <= 0) {
    console.warn('Invalid propertyId:', propertyId);
    return;
  }

  fetch(`./favorites.php?property_id=${propertyId}`, {
    method: 'GET',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin'
  })
    .then(res => {
      if (res.status === 401) {
        // Don't show message for unauthenticated users on page load
        iconElement.classList.remove('fa-solid', 'favorited');
        iconElement.classList.add('fa-regular');
        return;
      }
      if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (!data) return;
      if (data.favorited) {
        iconElement.classList.remove('fa-regular');
        iconElement.classList.add('fa-solid', 'favorited');
      } else {
        iconElement.classList.remove('fa-solid', 'favorited');
        iconElement.classList.add('fa-regular');
      }
    })
    .catch(err => {
      console.error('Favorite status check failed:', err);
      // Default to fa-regular on error
      iconElement.classList.remove('fa-solid', 'favorited');
      iconElement.classList.add('fa-regular');
    });
}

function toggleFavorite(propertyId, iconElement) {
  if (!propertyId || isNaN(propertyId) || propertyId <= 0) {
    console.warn('Invalid propertyId:', propertyId);
    return;
  }

  const action = iconElement.classList.contains('fa-solid') ? 'remove' : 'add';

  fetch('./favorites', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin',
    body: JSON.stringify({ property_id: propertyId, action })
  })
    .then(res => {
      if (res.status === 401) {
        showResponseMessage('Please log in to favorite properties.');
        return;
      }
      if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
      return res.json();
    })
    .then(data => {
      if (!data) return;
      showResponseMessage(data.message || 'Updated');

      if (data.favorited) {
        iconElement.classList.remove('fa-regular');
        iconElement.classList.add('fa-solid', 'favorited');
      } else {
        iconElement.classList.remove('fa-solid', 'favorited');
        iconElement.classList.add('fa-regular');
      }
    })
    .catch(err => {
      console.error('Favorite toggle failed:', err);
      showResponseMessage('Something went wrong.');
    });
}

function reinitializeHeartIcons() {
  const heartIcons = document.querySelectorAll('.heart-icon');

  heartIcons.forEach(icon => {
    const clonedIcon = icon.cloneNode(true);
    icon.parentNode.replaceChild(clonedIcon, icon);

    // Default to fa-regular
    clonedIcon.classList.remove('fa-solid', 'favorited');
    clonedIcon.classList.add('fa-regular');

    const propertyId = parseInt(clonedIcon.getAttribute('data-property-id'));
    if (propertyId && !isNaN(propertyId)) {
      updateFavoriteIcon(propertyId, clonedIcon);
    }

    clonedIcon.addEventListener('click', event => {
      preventHeartIconLinkBehavior(event);
      const propId = parseInt(clonedIcon.getAttribute('data-property-id'));
      if (propId && !isNaN(propId)) {
        toggleFavorite(propId, clonedIcon);
      }
    });
  });
}

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

window.reinitializeAllSwipers = reinitializeAllSwipers;
window.reinitializeHeartIcons = reinitializeHeartIcons;

document.addEventListener("DOMContentLoaded", () => {
  reinitializeAllSwipers();
  reinitializeHeartIcons();
});