document.addEventListener ('DOMContentLoaded', function () {
  const sections = document.querySelectorAll ('section[id]');
  const navLinks = document.querySelectorAll ('header .nav-link');
  const headerEl = document.querySelector ('header');

  // Mapeo opcional si los IDs de secciones no coinciden con los href
  const sectionToNavLink = {
    AboutUs: '#AboutUs',
    TheTeam: '#TheTeam',
    brands: '#TheTeam',
    KeyInvestments: '#KeyInvestments',
    OurNetwork: '#OurNetwork',
    Contact: '#Contact',
  };

  let observer;

  function observerCallback (entries) {
    /*
     * 1) Filtramos solamente las secciones que están
     *    "intersectando" (es decir, tienen algo de visibilidad).
     */
    const visibleSections = entries.filter (entry => entry.isIntersecting);

    // Si no hay secciones visibles, no hay nada que actualizar.
    if (visibleSections.length === 0) return;

    /*
     * 2) Ordenamos de mayor a menor intersectionRatio.
     *    La primera en la lista será la "más visible"
     *    en este instante.
     */
    visibleSections.sort ((a, b) => b.intersectionRatio - a.intersectionRatio);

    const topEntry = visibleSections[0]; // la de mayor ratio
    const topId = topEntry.target.id;

    // 3) Quitamos .active de todos los links antes de asignar
    navLinks.forEach (link => link.classList.remove ('active'));

    // 4) Construimos el selector del link correspondiente a esa sección
    let activeLinkSelector = `header .nav-link[href="#${topId}"]`;
    if (sectionToNavLink.hasOwnProperty (topId)) {
      activeLinkSelector = `header .nav-link[href="${sectionToNavLink[topId]}"]`;
    }

    // 5) Agregamos .active al link de la sección más visible
    const activeLink = document.querySelector (activeLinkSelector);
    if (activeLink) {
      activeLink.classList.add ('active');
    }

    // 6) Si es la sección Contact, agregamos .bg-light al header
    //    En caso contrario, la removemos
    if (topId === 'Contact') {
      headerEl.classList.add ('bg-white');
    } else {
      headerEl.classList.remove ('bg-white');
    }
  }

  function createObserver () {
    if (observer) observer.disconnect ();

    observer = new IntersectionObserver (observerCallback, {
      root: null,
      rootMargin: '0px',
      // Usar un único valor de threshold reduce el parpadeo:
      threshold: 0.5,
    });

    sections.forEach (section => observer.observe (section));
  }

  createObserver ();

  // Re-crear el observer al hacer resize, por si cambian las dimensiones
  window.addEventListener ('resize', () => {
    createObserver ();
  });
});
