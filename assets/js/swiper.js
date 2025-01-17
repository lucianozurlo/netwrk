// Función genérica para manejar visibilidad con animaciones
function toggleVisibility (element, condition) {
  if (condition) {
    if (!element.classList.contains ('visible')) {
      element.classList.remove ('fade-out');
      element.classList.add ('visible');
    }
  } else {
    if (element.classList.contains ('visible')) {
      element.classList.add ('fade-out');
      element.classList.remove ('visible');
      element.addEventListener ('animationend', function handler () {
        element.style.zIndex = '1';
        element.classList.remove ('fade-out');
        element.removeEventListener ('animationend', handler);
      });
    }
  }
}

// Función para inicializar Swiper en una sección específica
function initializeSwipers (containerSelector) {
  const swiper = new Swiper (containerSelector + ' .mySwiper', {
    pagination: {
      el: containerSelector + ' .swiper-pagination',
      clickable: true,
      renderBullet: (index, className) =>
        `<span class="${className}">${index + 1}</span>`,
    },
    autoHeight: true,
    observer: true,
    observeParents: true,
    speed: 500,
  });

  const links = document.querySelectorAll (containerSelector + ' .go-to-slide');
  const highlight = document.querySelector (containerSelector + ' .highlight');

  function updateActiveLink () {
    const currentIndex = swiper.activeIndex;
    links.forEach (link => link.classList.remove ('active'));

    const currentLink = document.querySelector (
      `${containerSelector} .go-to-slide[data-slide="${currentIndex + 1}"]`
    );

    if (currentLink) {
      currentLink.classList.add ('active');
      moveHighlight (currentLink);
    }
  }

  function moveHighlight (link) {
    const linkRect = link.getBoundingClientRect ();
    const containerRect = link.parentNode.getBoundingClientRect ();

    const leftPos = linkRect.left - containerRect.left;
    const width = linkRect.width;

    highlight.style.left = `${leftPos}px`;
    highlight.style.width = `${width}px`;
  }

  links.forEach (link => {
    link.addEventListener ('click', e => {
      e.preventDefault ();
      const slideIndex = parseInt (link.dataset.slide, 10) - 1;
      swiper.slideTo (slideIndex);
    });
  });

  swiper.on ('slideChange', () => {
    updateActiveLink ();

    setTimeout (() => {
      const container = document.querySelector (
        containerSelector + ' .swiper-container'
      );

      if (containerSelector === '#KeyInvestments') {
        container.style.maxWidth = swiper.activeIndex === 2
          ? '760px'
          : '1150px';
      } else {
        container.style.maxWidth = swiper.activeIndex === 1
          ? '760px'
          : '1150px';
      }

      swiper.updateAutoHeight ();
      swiper.update ();
    }, 350);
  });

  // Ajustar altura igual al iniciar en responsive
  setEqualHeight (swiper);

  // Ajustar altura igual al redimensionar
  window.addEventListener ('resize', () => {
    setEqualHeight (swiper);
  });

  updateActiveLink ();
}

// Inicializar Swipers cuando el DOM esté cargado
document.addEventListener ('DOMContentLoaded', () => {
  initializeSwipers ('#Contact');
  initializeSwipers ('#KeyInvestments');
});

// Swiper para Mobile
const swiperMobile = new Swiper ('.mySwiperMob', {
  slidesPerView: 'auto',
  centeredSlides: true,
  spaceBetween: 20,
});
