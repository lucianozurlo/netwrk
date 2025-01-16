document.addEventListener ('DOMContentLoaded', function () {
  const sections = document.querySelectorAll ('section[id]');
  const navLinks = document.querySelectorAll ('header .nav-link');
  const highlight = document.querySelector ('.nav-highlight');
  const firstLink = navLinks[0]; // Primer link en tu menú (ej: Services)

  let observer; // Variable global para IntersectionObserver

  // ---------------------------
  // 1) Función para saber si vienes "desde about"
  //    Leyendo el flag 'fromAbout' en sessionStorage
  // ---------------------------
  function isFromAbout () {
    const fromAbout = sessionStorage.getItem ('fromAbout');
    if (fromAbout === 'true') {
      sessionStorage.removeItem ('fromAbout'); // Limpiar la bandera para futuras navegaciones
      return true;
    }
    return false;
  }

  // ---------------------------
  // 2) Función para obtener threshold dinámico
  // ---------------------------
  function getThreshold () {
    // Si vienes de about.html => Forzamos 0.6
    if (isFromAbout ()) {
      console.log ('Threshold forzado a 0.6 porque vienes de about.html');
      return 0.6;
    }

    // Si NO vienes de about.html,
    // desktop >= 1000 => threshold 0.6
    // mobile < 1000 => threshold 0
    const thresholdValue = window.innerWidth >= 1000 ? 0.6 : 0;
    console.log (
      `Threshold dinámico establecido a ${thresholdValue} (window width: ${window.innerWidth}px)`
    );
    return thresholdValue;
  }

  // ---------------------------
  // 3) Callback del IntersectionObserver
  // ---------------------------
  function observerCallback (entries) {
    // Mapeo de secciones -> links
    const sectionToNavLink = {
      services: '#services',
      tools: '#services',
      // Agrega más si deseas
    };

    entries.forEach (entry => {
      if (entry.isIntersecting) {
        // Remover 'active' de todos los enlaces
        navLinks.forEach (link => link.classList.remove ('active'));

        let activeLinkSelector = `header .nav-link[href="#${entry.target.id}"]`;
        if (sectionToNavLink.hasOwnProperty (entry.target.id)) {
          activeLinkSelector = `header .nav-link[href="${sectionToNavLink[entry.target.id]}"]`;
        }

        const activeLink = document.querySelector (activeLinkSelector);
        if (activeLink) {
          activeLink.classList.add ('active');
          moveHighlight (activeLink);
          highlight.classList.remove ('hide');
        } else {
          // No hay link activo => mover highlight al primer link y ocultar
          moveHighlight (firstLink);
          highlight.classList.add ('hide');
        }
      }
    });
  }

  // ---------------------------
  // 4) Crear (o recrear) el IntersectionObserver con el threshold adecuado
  // ---------------------------
  function createObserver () {
    // Si ya existía un observer, lo desconectamos
    if (observer) {
      observer.disconnect ();
    }

    observer = new IntersectionObserver (observerCallback, {
      root: null,
      rootMargin: '0px 0px 0px 0px', // Ajusta si tu header tapa algo
      // threshold: getThreshold (),
    });

    // Observar cada sección
    sections.forEach (section => observer.observe (section));
    // console.log ('IntersectionObserver creado con threshold:', getThreshold ());
  }

  // ---------------------------
  // 5) Función para mover la barra highlight
  // ---------------------------
  function moveHighlight (activeLink) {
    const linkRect = activeLink.getBoundingClientRect ();
    const containerRect = activeLink.parentElement.parentElement.getBoundingClientRect ();

    if (window.innerWidth >= 1000) {
      // Desktop (horizontal)
      const left = linkRect.left - containerRect.left;
      const width = linkRect.width;
      highlight.style.left = `${left}px`;
      highlight.style.width = `${width}px`;
      highlight.style.top = `auto`;
      highlight.style.bottom = `0`;
      // highlight.style.height = '35px'; // Solo si quieres altura fija
      console.log (`Highlight movido a Desktop: left=${left}, width=${width}`);
    } else {
      // Mobile (vertical)
      const top = linkRect.top - containerRect.top;
      const left = linkRect.left - containerRect.left;
      const width = linkRect.width;
      const height = linkRect.height;

      highlight.style.top = `${top}px`;
      highlight.style.left = `${left}px`;
      highlight.style.width = `${width}px`;
      highlight.style.height = `${height}px`;
      highlight.style.bottom = 'auto';
      console.log (
        `Highlight movido a Mobile: top=${top}, left=${left}, width=${width}, height=${height}`
      );
    }
  }

  // ---------------------------
  // 6) Inicializar el observer
  // ---------------------------
  createObserver (); // Crea el observer con el threshold correcto

  // Chequear si hay un link activo al cargar
  const initialActiveLink = document.querySelector ('header .nav-link.active');
  if (initialActiveLink) {
    moveHighlight (initialActiveLink);
    highlight.classList.remove ('hide');
  } else {
    // Mover highlight al primer link y ocultar
    moveHighlight (firstLink);
    highlight.classList.add ('hide');
  }

  // ---------------------------
  // 7) Reaccionar a cambios de tamaño de ventana
  // ---------------------------
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
