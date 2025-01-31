// preload.js

(function () {
  // 1. Referencias a elementos del DOM
  const preloader = document.querySelector ('.page-loading');
  const progressBar = document.querySelector ('.progress-bar');
  const progressContainer = document.querySelector ('.progress-bar-container');
  const progressText = document.createElement ('span'); // para mostrar el % dentro de la barra
  progressText.classList.add ('progress-text');
  progressText.textContent = '0%';
  progressBar.appendChild (progressText); // Inserta el texto en la barra

  const header = document.querySelector ('header'); // si tienes un header oculto de inicio

  // 2. Configuraciones
  const maxProgress = 120; // ancho máximo (px) de la barra personalizada

  /**
   * Función para obtener la lista de imágenes desde el servidor
   * (assets/php/list_images.php).
   */
  async function fetchImageList () {
    try {
      const response = await fetch ('assets/php/list_images.php');
      if (!response.ok) {
        const errorText = await response.text ();
        throw new Error (
          `Error fetching the image list: ${response.status} ${response.statusText} - ${errorText}`
        );
      }
      const imageList = await response.json ();
      console.log ('imageList:', imageList); // Log para depurar
      return imageList;
    } catch (error) {
      console.error ('fetchImageList Error:', error);
      // Devuelve un array vacío para no romper la lógica.
      return [];
    }
  }

  /**
   * Función para precargar imágenes con logs detallados y retardo artificial (opcional).
   * @param {Array} imagePaths - Lista de rutas de imágenes.
   * @param {Function} onProgress - Callback que recibe (loaded, total).
   * @param {Function} onComplete - Callback cuando termina la precarga.
   */
  function preloadImages (imagePaths, onProgress, onComplete) {
    if (!Array.isArray (imagePaths)) {
      console.error ('imagePaths no es un array:', imagePaths);
      onComplete ();
      return;
    }

    const totalImages = imagePaths.length;
    let imagesLoaded = 0;

    if (totalImages === 0) {
      onComplete ();
      return;
    }

    imagePaths.forEach ((path, index) => {
      // Retardo artificial (opcional para ver la barra avanzando)
      // Retarda la creación de la imagen entre 0 y 1 segundo:
      const fakeDelay = Math.random () * 1000; // entre 0ms y 1000ms

      setTimeout (() => {
        const img = new Image ();
        img.src = path;

        img.onload = img.onerror = () => {
          imagesLoaded++;
          console.log (`Imagen #${index + 1} cargada → ${path}`);
          onProgress (imagesLoaded, totalImages);

          // Si llegamos al total
          if (imagesLoaded === totalImages) {
            onComplete ();
          }
        };
      }, fakeDelay);
    });
  }

  /**
   * Función principal que inicializa el preloader.
   */
  async function initializePreloader () {
    // 1. Obtenemos la lista de imágenes desde PHP
    const imageList = await fetchImageList ();

    // 2. Chequeo de NProgress
    if (typeof NProgress === 'undefined') {
      console.error (
        'NProgress no está definido. Revisa la carga de nprogress.js.'
      );
      return;
    }

    // Configuración y arranque de NProgress
    NProgress.configure ({showSpinner: false});
    NProgress.start ();

    // 3. Iniciamos la precarga de imágenes
    preloadImages (
      imageList,
      (loaded, total) => {
        const progressPercentage = loaded / total * 100;

        // Actualizar la barra
        progressBar.style.width = `${loaded / total * maxProgress}px`;

        // Encontrar la única etiqueta
        const progressTextEl = progressBar.querySelector ('.progress-text');
        if (progressTextEl) {
          progressTextEl.textContent = `${Math.floor (progressPercentage)}%`;
        }

        // Log en consola
        console.log (
          `Cargando imágenes: ${loaded}/${total} (${Math.floor (progressPercentage)}%)`
        );
      },
      () => {
        // Cuando todas las imágenes listadas han sido precargadas
        console.log ('Todas las imágenes han sido precargadas.');

        // Podríamos esperar a que la página entera (window.load) esté lista
        // o simplemente finalizar el preloader de inmediato:
        finishPreloader ();
      }
    );
  }

  /**
   * Función para finalizar el preloader (ocultar y remover).
   */
  function finishPreloader () {
    // Terminamos NProgress
    NProgress.done ();

    // Ponemos la barra al 100% (opcional, por si no llegó antes)
    progressBar.style.width = `${maxProgress}px`;
    progressContainer.setAttribute ('aria-valuenow', maxProgress);

    preloader.classList.add ('hidden');
    preloader.setAttribute ('aria-hidden', 'true');

    // Cuando acabe la transición de opacidad, removemos el contenedor
    preloader.addEventListener ('transitionend', () => {
      preloader.remove ();

      // Mostrar header (si lo tenías oculto)
      if (header) {
        header.classList.remove ('hidden');
        header.classList.add ('visible');
      }

      console.log ('El preloader ha terminado y ha sido removido.');
    });
  }

  // 4. Al DOMContentLoaded, inicia la lógica
  document.addEventListener ('DOMContentLoaded', initializePreloader);
}) ();
