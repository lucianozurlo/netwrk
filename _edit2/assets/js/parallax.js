document.addEventListener ('DOMContentLoaded', function () {
  // Selecciona las imágenes que quieres que se muevan
  const bg1 = document.querySelector ('#home .bg1 img');
  const bg2 = document.querySelector ('#home .bg2 img');
  const aboutBg = document.querySelector ('#About .bg img');
  const keyInvBg = document.querySelector ('#KeyInvestments .bg img');
  const contactBg = document.querySelector ('#Contact .bg img');
  // Selecciona la sección #Home para el fondo parallax
  const homeSection = document.querySelector ('#home');

  // Variables para almacenar la posición inicial (x, y) y factores de cada elemento.
  // let offsetXBg1, offsetYBg1, factorBg1;
  // let offsetXBg2, offsetYBg2, factorBg2;
  let factorBg1, factorBg2, factorAbout, factorKeyInv, factorContact;

  // Para #Home
  let baseYHome; // Offset inicial en % (si lo deseas distinto para mobile/desktop)
  let factorHome; // Factor de desplazamiento para #Home

  /**
   * Determina qué offsets y factores usar en desktop vs. mobile.
   * Llamar esta función al cargar la página y al cambiar el tamaño (resize).
   */
  function updateOffsets () {
    // Ajusta tu breakpoint a conveniencia. Ej: <=999 es "mobile"
    if (window.innerWidth <= 999) {
      console.log (`El window.innerWidth es: ${window.innerWidth}`);
      console.log (window.innerWidth <= 999);
      console.log ('MOBILE');

      // VERSIÓN MOBILE
      baseYBg1 = '70%';
      factorBg1 = 0.1;

      // Offsets y factores para #home .bg2
      baseYBg2 = '-14%';
      factorBg2 = 0.15;

      // Factores para aboutBg y keyInvBg
      baseYAbout = '0%';
      factorAbout = 0.05;

      baseYKeyInv = '25%';
      factorKeyInv = 0.05;

      baseYContact = '25%';
      factorContact = 0.05;

      // Para #Home
      // Ejemplo: comienza en "25%" y se mueve más suave
      baseYHome = '25%';
      factorHome = 0.07;
    } else {
      console.log ('DESKTOP');

      // VERSIÓN DESKTOP
      // Offsets y factores para #home .bg1
      baseYBg1 = '45%';
      factorBg1 = 0.2;

      // Offsets y factores para #home .bg2
      baseYBg2 = '-11%';
      factorBg2 = 0.3;

      // Factores para aboutBg y keyInvBg

      baseYAbout = '0%';
      factorAbout = 0.1;

      baseYKeyInv = '57%';
      factorKeyInv = 0.1;

      baseYContact = '515%';
      factorContact = 0.07;

      // Para #Home
      // Ejemplo: comienza en "25%" y se mueve un poco más rápido
      baseYHome = '25%';
      factorHome = 0.07;
    }
  }

  // Llamamos una vez al cargar
  updateOffsets ();

  // Volvemos a llamar si se redimensiona la ventana (para recalcular)
  window.addEventListener ('resize', updateOffsets);

  // Escuchamos el evento scroll
  window.addEventListener ('scroll', () => {
    // Cantidad de scroll vertical
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;

    // bg1 => offsetXBg1, offsetYBg1 + desplazamiento
    // if (bg1) {
    //   bg1.style.transform = `translate(${offsetXBg1}, calc(${offsetYBg1} + ${scrollY * factorBg1}px))`;
    // }

    // bg2 => offsetXBg2, offsetYBg2 + desplazamiento
    // if (bg2) {
    //   bg2.style.transform = `translate(${offsetXBg2}, calc(${offsetYBg2} + ${scrollY * factorBg2}px))`;
    // }

    // Imágenes de About / KeyInvestments / Contact (solo factor en Y)

    if (bg1) {
      bg1.style.top = `calc(${baseYBg1} - ${scrollY * factorBg1}px)`;
    }
    if (bg2) {
      bg2.style.top = `calc(${baseYBg2} - ${scrollY * factorBg2}px)`;
    }
    if (aboutBg) {
      aboutBg.style.top = `calc(${baseYAbout} - ${scrollY * factorAbout}px)`;
    }
    if (keyInvBg) {
      keyInvBg.style.top = `calc(${baseYKeyInv} - ${scrollY * factorKeyInv}px)`;
    }
    if (contactBg) {
      contactBg.style.top = `calc(${baseYContact} - ${scrollY * factorContact}px)`;
    }

    // Fondo de la sección #Home (si existe)
    if (homeSection) {
      // Combina la posición "right <algún %>" con un desplazamiento en px
      homeSection.style.backgroundPosition = `left calc(${baseYHome} - ${scrollY * factorHome}px)`;
    }
  });
});
