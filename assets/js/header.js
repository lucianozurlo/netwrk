document.addEventListener ('DOMContentLoaded', function () {
  const sections = document.querySelectorAll ('section[id]');
  const navLinks = document.querySelectorAll ('header .nav-link');
  const highlight = document.querySelector ('.nav-highlight');
  const firstLink = navLinks[0];

  let observer;

  function observerCallback (entries) {
    // Mapeo de secciones -> links, si es que los IDs no coinciden
    const sectionToNavLink = {
      AboutUs: '#AboutUs',
      TheTeam: '#TheTeam',
      brands: '#TheTeam',
      KeyInvestments: '#KeyInvestments',
      OurNetwork: '#OurNetwork',
      Contact: '#Contact',
    };

    entries.forEach (entry => {
      if (entry.isIntersecting) {
        // Removemos 'active' de todos los enlaces
        navLinks.forEach (link => link.classList.remove ('active'));

        // Por default, se asume que el link href="#<idDeLaSeccion>"
        let activeLinkSelector = `header .nav-link[href="#${entry.target.id}"]`;

        // Pero si los IDs difieren, usamos el mapeo
        if (sectionToNavLink.hasOwnProperty (entry.target.id)) {
          activeLinkSelector = `header .nav-link[href="${sectionToNavLink[entry.target.id]}"]`;
        }

        const activeLink = document.querySelector (activeLinkSelector);
        if (activeLink) {
          activeLink.classList.add ('active');
          // Si quieres seguir usando tu barra highlight, la mueves:
          moveHighlight (activeLink);
          highlight.classList.remove ('hide');
        } else {
          // Caso en que ninguna sección coincida
          moveHighlight (firstLink);
          highlight.classList.add ('hide');
        }
      }
    });
  }

  function createObserver () {
    if (observer) observer.disconnect ();

    observer = new IntersectionObserver (observerCallback, {
      root: null,
      rootMargin: '0px 0px 0px 0px',
      // threshold: 0.3 o el que desees
      threshold: 0.3,
    });

    sections.forEach (section => observer.observe (section));
  }

  function moveHighlight (activeLink) {
    // ... todo tu código existente para la barra highlight ...
    // Puedes eliminarlo si ya no quieres la barra animada
  }

  // Inicialización
  createObserver ();

  // En la carga inicial
  const initialActiveLink = document.querySelector ('header .nav-link.active');
  if (initialActiveLink) {
    moveHighlight (initialActiveLink);
    highlight.classList.remove ('hide');
  } else {
    moveHighlight (firstLink);
    highlight.classList.add ('hide');
  }

  // Manejo de resize
  window.addEventListener ('resize', () => {
    createObserver ();
    const activeLink = document.querySelector ('header .nav-link.active');
    if (activeLink) {
      moveHighlight (activeLink);
      highlight.classList.remove ('hide');
    } else {
      moveHighlight (firstLink);
      highlight.classList.add ('hide');
    }
  });
});
