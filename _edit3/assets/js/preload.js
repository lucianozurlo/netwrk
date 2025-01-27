// preload.js

(function () {
  const preloader = document.querySelector ('.page-loading');
  const progressBar = document.querySelector ('.progress-bar');
  const progressContainer = document.querySelector ('.progress-bar-container');

  // Variables para rastrear el progreso
  const maxProgress = 120; // Ancho máximo en píxeles para la barra personalizada

  /**
   * Function to fetch the list of images from the server.
   * @returns {Promise<Array>} List of image paths.
   */
  async function fetchImageList () {
    try {
      const response = await fetch ('assets/php/list_images.php'); // Ruta corregida
      if (!response.ok) {
        const errorText = await response.text ();
        throw new Error (
          `Error fetching the image list: ${response.status} ${response.statusText} - ${errorText}`
        );
      }
      const imageList = await response.json ();
      return imageList;
    } catch (error) {
      console.error (error);
      return [];
    }
  }

  /**
   * Function to preload images.
   * @param {Array} imagePaths List of image paths.
   * @param {Function} onProgress Callback for progress updates.
   * @param {Function} onComplete Callback when all images are loaded.
   */
  function preloadImages (imagePaths, onProgress, onComplete) {
    const totalImages = imagePaths.length;
    let imagesLoaded = 0;

    if (totalImages === 0) {
      onComplete ();
      return;
    }

    imagePaths.forEach (relativePath => {
      const img = new Image ();
      img.src = relativePath; // La ruta ya incluye la ruta relativa completa

      img.onload = img.onerror = () => {
        imagesLoaded++;
        onProgress (imagesLoaded, totalImages);
        if (imagesLoaded === totalImages) {
          onComplete ();
        }
      };
    });
  }

  /**
   * Función principal para manejar el preloader y la barra de progreso.
   */
  async function initializePreloader () {
    const imageList = await fetchImageList ();

    // Iniciar NProgress
    NProgress.configure ({showSpinner: false}); // Opcional: Ocultar el spinner de NProgress
    NProgress.start ();

    preloadImages (
      imageList,
      (loaded, total) => {
        const progressPercentage = loaded / total * 100;
        // Actualizar NProgress
        NProgress.set (progressPercentage / 100);

        // Actualizar barra de progreso personalizada
        progressBar.style.width = `${loaded / total * maxProgress}px`; // Ancho fijo de 120px
        progressContainer.setAttribute ('aria-valuenow', loaded);
      },
      () => {
        // Completar NProgress
        NProgress.done ();

        // Completar la barra de progreso personalizada
        progressBar.style.width = `${maxProgress}px`;
        progressContainer.setAttribute ('aria-valuenow', maxProgress);

        // **Console Log cuando se han precargado todas las imágenes**
        console.log ('Todas las imágenes han sido precargadas.');

        // Esperar un breve momento antes de iniciar la transición de desvanecimiento
        setTimeout (() => {
          preloader.classList.add ('hidden');
          preloader.setAttribute ('aria-hidden', 'true');

          // Remover el preloader del DOM después de la transición
          preloader.addEventListener ('transitionend', () => {
            preloader.remove ();
            // **Console Log cuando ha terminado el preloader**
            console.log ('El preloader ha terminado y ha sido removido.');
          });
        }, 500); // 500ms de espera antes de iniciar la transición
      }
    );
  }

  // Ejecutar el preload cuando el DOM esté completamente cargado
  document.addEventListener ('DOMContentLoaded', initializePreloader);
}) ();
