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
      // VERSIÓN MOBILE
      // Offsets y factores para #home .bg1
      // offsetXBg1 = '-39%';
      // offsetYBg1 = '33%';

      baseYBg1 = '25%';
      factorBg1 = 0.1;

      // Offsets y factores para #home .bg2
      // offsetXBg2 = '30%';
      // offsetYBg2 = '-40%';

      baseYBg2 = '25%';
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
      // VERSIÓN DESKTOP
      // Offsets y factores para #home .bg1
      // offsetXBg1 = '0%';
      // offsetYBg1 = '25%';
      baseYBg1 = '25%';
      factorBg1 = 0.2;

      // Offsets y factores para #home .bg2
      // offsetXBg2 = '0%';
      // offsetYBg2 = '-15%';
      baseYBg2 = '-15%';
      factorBg2 = 0.3;

      // Factores para aboutBg y keyInvBg

      baseYAbout = '0%';
      factorAbout = 0.1;

      baseYKeyInv = '25%';
      factorKeyInv = 0.1;

      baseYContact = '25%';
      factorContact = 0.1;

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
      bg1.style.transform = `translateY(calc(${baseYBg1} - ${scrollY * factorBg1}px))`;
    }
    if (bg2) {
      bg2.style.transform = `translateY(calc(${baseYBg2} - ${scrollY * factorBg2}px))`;
    }
    if (aboutBg) {
      aboutBg.style.transform = `translateY(calc(${baseYAbout} - ${scrollY * factorAbout}px))`;
    }
    if (keyInvBg) {
      keyInvBg.style.transform = `translateY(calc(${baseYKeyInv} - ${scrollY * factorKeyInv}px))`;
    }
    if (contactBg) {
      contactBg.style.transform = `translateY(calc(${baseYContact} - ${scrollY * factorContact}px))`;
    }

    // Fondo de la sección #Home (si existe)
    if (homeSection) {
      // Combina la posición "right <algún %>" con un desplazamiento en px
      homeSection.style.backgroundPosition = `left calc(${baseYHome} - ${scrollY * factorHome}px)`;
    }
  });
});
