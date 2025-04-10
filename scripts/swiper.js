// Create a reusable function to initialize a Swiper instance
function initializeSwiper(swiperContainer) {
  const nextButton = swiperContainer.querySelector('.swiper-button-next');
  const prevButton = swiperContainer.querySelector('.swiper-button-prev');

  // 👇 Set slidesPerView based on the class
  const isFeedbackSwiper = swiperContainer.classList.contains('feedback-swiper');

  const swiper = new Swiper(swiperContainer, {
    loop: true,
    slidesPerView: isFeedbackSwiper ? 2 : 1,  // ✅ Only .feedback-swiper shows 2
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


document.addEventListener("DOMContentLoaded", () => {
  const allSwipers = document.querySelectorAll('.mySwiper, .feedback-swiper');
  allSwipers.forEach(swiperContainer => {
    initializeSwiper(swiperContainer);
  });
});
