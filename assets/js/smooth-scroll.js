function smoothScrollTo (targetId) {
  const targetElement = document.getElementById (targetId);

  if (targetElement) {
    const offsetTop =
      targetElement.getBoundingClientRect ().top + window.pageYOffset - 110;

    window.scrollTo ({
      top: offsetTop,
      behavior: 'smooth',
    });
  }
}

document.addEventListener ('DOMContentLoaded', () => {
  // Manejar los enlaces con clase .scroll-link
  document.querySelectorAll ('.scroll-link').forEach (link => {
    link.addEventListener ('click', function (e) {
      e.preventDefault ();

      const targetId = this.getAttribute ('href').slice (1);
      smoothScrollTo (targetId);
    });
  });

  // Manejar la animación de los elementos al entrar en el viewport
  const animateElements = document.querySelectorAll ('.scroll-animate');
  let delay = 0;

  const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.05,
  };

  const observer = new IntersectionObserver ((entries, observer) => {
    entries.forEach (entry => {
      if (entry.isIntersecting) {
        entry.target.style.setProperty ('--delay', `${delay}s`);
        entry.target.classList.add ('visible');
        delay += 0.025;
        observer.unobserve (entry.target);
      }
    });
  }, observerOptions);

  animateElements.forEach (el => {
    observer.observe (el);
  });

  // Manejar el desplazamiento suave si hay un hash en la URL al cargar la página
  if (window.location.hash) {
    const targetId = window.location.hash.slice (1);
    // Usar setTimeout para asegurar que el DOM esté completamente cargado
    setTimeout (() => {
      smoothScrollTo (targetId);
    }, 100); // Puedes ajustar el tiempo si es necesario
  }
});
