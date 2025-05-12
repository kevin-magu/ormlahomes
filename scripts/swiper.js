//start of js

document.addEventListener("DOMContentLoaded", function () {
  // ✅ Define global login status
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
        init() {
          updateButtonVisibility(this);
          reinitializeHeartIcons(); // Ensure icons are initialized after swiper renders
        },
        slideChange() {
          updateButtonVisibility(this);
          reinitializeHeartIcons(); // Re-run in case new slides have new icons
        },
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
      body: JSON.stringify({ property_id: propertyId, action }),
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

      .then(() => {
        updateFavoriteCount(); // Refresh navbar count
      })
    
      .catch(err => {
        console.error('Favorite toggle failed:', err);
        showResponseMessage('Something went wrong.');
      });
  }
  const isLoggedIn = window.isLoggedIn || localStorage.getItem('isLoggedIn') === 'true';
  function reinitializeHeartIcons() {
    const heartIcons = document.querySelectorAll('.heart-icon');
  
    heartIcons.forEach(icon => {
      const clonedIcon = icon.cloneNode(true);
      icon.parentNode.replaceChild(clonedIcon, icon);
  
      clonedIcon.classList.remove('fa-solid', 'favorited');
      clonedIcon.classList.add('fa-regular');
      
      
      

      const propertyId = parseInt(clonedIcon.getAttribute('data-property-id'));
      if (propertyId && !isNaN(propertyId) && isLoggedIn) {
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
  console.log("window.isLoggedIn =" ,isLoggedIn);
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
  
  // Expose functions globally
  window.reinitializeAllSwipers = reinitializeAllSwipers;
  window.reinitializeHeartIcons = reinitializeHeartIcons;
  
    reinitializeAllSwipers();
    reinitializeHeartIcons()
  
    // Set a small delay to catch late-rendered icons
    setTimeout(() => {
      reinitializeHeartIcons();
    }, 300);

    const filterButton = document.querySelector('.filter-button-container button');
    const filterContainer = document.getElementById('propertyTypes');

    if (filterButton && filterContainer) {
      const closeIcon = filterContainer.querySelector('.fa-rectangle-xmark');

      // Open filter panel
      filterButton.addEventListener('click', function () {
        filterContainer.style.display = 'flex';
      });

      // Close when clicking the close icon
      if (closeIcon) {
        closeIcon.addEventListener('click', function () {
          filterContainer.style.display = 'none';
        });
      }

      // Close when clicking outside
      document.addEventListener('click', function (e) {
        const isClickInside = filterContainer.contains(e.target) || filterButton.contains(e.target);
        if (!isClickInside) {
          filterContainer.style.display = 'none';
        }
      });
    }


    
});

//end of js


