document.addEventListener ('DOMContentLoaded', function () {
  const lazyImages = document.querySelectorAll ('img.lazy');

  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver ((entries, observer) => {
      entries.forEach (entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove ('lazy');
          imageObserver.unobserve (img);
        }
      });
    });

    lazyImages.forEach (img => {
      imageObserver.observe (img);
    });
  } else {
    // Fallback para navegadores que no soportan Intersection Observer
    const lazyLoad = () => {
      lazyImages.forEach (img => {
        if (
          img.getBoundingClientRect ().top < window.innerHeight &&
          img.getBoundingClientRect ().bottom > 0 &&
          getComputedStyle (img).display !== 'none'
        ) {
          img.src = img.dataset.src;
          img.classList.remove ('lazy');
        }
      });

      if (lazyImages.length === 0) {
        document.removeEventListener ('scroll', lazyLoad);
        window.removeEventListener ('resize', lazyLoad);
        window.removeEventListener ('orientationchange', lazyLoad);
      }
    };

    document.addEventListener ('scroll', lazyLoad);
    window.addEventListener ('resize', lazyLoad);
    window.addEventListener ('orientationchange', lazyLoad);
  }
});
