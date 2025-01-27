(function () {
  const preloader = document.querySelector ('.page-loading');
  const progressBar = document.querySelector ('.progress-bar');
  const progressContainer = document.querySelector ('.progress-bar-container');

  let progress = 0;
  const maxProgress = 120; // Ancho máximo en píxeles

  // Iniciar NProgress
  NProgress.configure ({showSpinner: false}); // Opcional: Ocultar el spinner de NProgress
  NProgress.start ();

  // Obtener todas las imágenes de la página
  const images = document.images;
  const totalImages = images.length;
  let imagesLoaded = 0;

  if (totalImages === 0) {
    // Si no hay imágenes, completar la carga
    completeLoad ();
  } else {
    // Añadir evento load a cada imagen
    for (let img of images) {
      img.addEventListener ('load', imageLoaded);
      img.addEventListener ('error', imageLoaded); // Contar también las imágenes que fallan
    }
  }

  function imageLoaded () {
    imagesLoaded++;
    updateProgress (imagesLoaded / totalImages);
    if (imagesLoaded === totalImages) {
      completeLoad ();
    }
  }

  function updateProgress (ratio) {
    // Actualizar progreso simulado basado en la proporción de imágenes cargadas
    progress = Math.min (Math.floor (ratio * maxProgress), maxProgress);
    progressBar.style.width = progress + 'px';
    progressContainer.setAttribute ('aria-valuenow', progress);

    // Actualizar NProgress
    NProgress.set (ratio);
  }

  function completeLoad () {
    // Completar NProgress
    NProgress.done ();

    // Completar la barra de progreso simulada
    progress = maxProgress;
    progressBar.style.width = progress + 'px';
    progressContainer.setAttribute ('aria-valuenow', progress);

    // Esperar un breve momento antes de iniciar la transición de desvanecimiento
    setTimeout (() => {
      preloader.classList.add ('hidden');
      preloader.setAttribute ('aria-hidden', 'true');

      // Remover el preloader del DOM después de la transición
      preloader.addEventListener ('transitionend', () => {
        preloader.remove ();
      });
    }, 500); // 500ms de espera antes de iniciar la transición
  }

  // Opcional: Si tienes cargas adicionales después del load, puedes controlar NProgress aquí
  /*
  // Ejemplo de carga adicional
  fetch('/api/data')
      .then(response => response.json())
      .then(data => {
          // Procesar datos
          NProgress.done();
      })
      .catch(error => {
          console.error('Error:', error);
          NProgress.done();
      });
  */
}) ();
