document.addEventListener ('DOMContentLoaded', function () {
  // Selecciona las imágenes que quieres que se muevan
  const bg1 = document.querySelector ('#home .bg1 img');
  const bg2 = document.querySelector ('#home .bg2 img');
  const aboutBg = document.querySelector ('#About .bg img');
  const keyInvBg = document.querySelector ('#KeyInvestments .bg img');

  // Variables para almacenar la posición inicial (x, y) según el tamaño de pantalla
  let offsetXBg1, offsetYBg1, offsetXBg2, offsetYBg2;

  // Factores de velocidad para cada imagen
  // NOTA: los definimos como "let" para reescribirlos dentro de "updateOffsets()"
  let factorBg1, factorBg2, factorAbout, factorKeyInv;

  /**
     * Determina qué offsets y factores usar en desktop vs. mobile.
     * Llamar esta función al cargar la página y al cambiar el tamaño (resize).
     */
  function updateOffsets () {
    // Elige un breakpoint. Ej.: <= 999 es mobile, > 999 es desktop
    if (window.innerWidth <= 999) {
      // VERSIÓN MOBILE
      // Offsets
      offsetXBg1 = '-39%';
      offsetYBg1 = '33%';
      offsetXBg2 = '30%';
      offsetYBg2 = '-40%';

      // Factores (pon los que necesites para mobile)
      factorBg1 = 0.1;
      factorBg2 = 0.15;
      factorAbout = 0.05;
      factorKeyInv = 0.05;
    } else {
      // VERSIÓN DESKTOP
      // Offsets
      offsetXBg1 = '0%';
      offsetYBg1 = '25%';
      offsetXBg2 = '0%';
      offsetYBg2 = '-15%';

      // Factores (pon los que necesites para desktop)
      factorBg1 = 0.2;
      factorBg2 = 0.3;
      factorAbout = 0.1;
      factorKeyInv = 0.1;
    }
  }

  // Llamamos una vez al cargar
  updateOffsets ();

  // Volvemos a llamar si se redimensiona la ventana (para cambiar offsets y factores en vivo)
  window.addEventListener ('resize', updateOffsets);

  // Escuchamos el evento scroll
  window.addEventListener ('scroll', () => {
    // Cantidad de scroll vertical
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;

    // bg1 => offsetXBg1, offsetYBg1 + desplazamiento
    if (bg1) {
      bg1.style.transform = `translate(${offsetXBg1}, calc(${offsetYBg1} + ${scrollY * factorBg1}px))`;
    }

    // bg2 => offsetXBg2, offsetYBg2 + desplazamiento
    if (bg2) {
      bg2.style.transform = `translate(${offsetXBg2}, calc(${offsetYBg2} + ${scrollY * factorBg2}px))`;
    }

    // Las demás imágenes se mantienen igual, pero con sus factores
    if (aboutBg) {
      aboutBg.style.transform = `translateY(${scrollY * factorAbout}px)`;
    }
    if (keyInvBg) {
      keyInvBg.style.transform = `translateY(${scrollY * factorKeyInv}px)`;
    }
  });
});
