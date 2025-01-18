/////
function openNav () {
  document.querySelector ('#navbarNav').classList.add ('active');
}
function closeNav () {
  document.querySelector ('#navbarNav').classList.remove ('active');
}

/////
document.addEventListener ('DOMContentLoaded', () => {
  const cards = document.querySelectorAll ('.cardX');
  let maxHeight = 0;

  cards.forEach (card => {
    card.style.height = 'auto';
    const cardHeight = card.offsetHeight;
    if (cardHeight > maxHeight) {
      maxHeight = cardHeight;
    }
  });

  cards.forEach (card => {
    card.style.height = `${maxHeight}px`;
  });
});

/////
const targetIds = [
  '#peregrine',
  '#spacex',
  '#arena',
  '#mikeshothoney',
  '#coframe',
  '#hypercard',
];
const observerTargets = targetIds.map (id => document.querySelector (id));

const observerCallback = mutationsList => {
  mutationsList.forEach (mutation => {
    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
      const targetElement = mutation.target;
      if (targetElement.classList.contains ('active')) {
        // Obtenemos la clase que corresponde al id actual
        const relatedClass = targetElement.id;
        // Buscamos el elemento correspondiente en .box-shadow-container
        const boxShadowElement = document.querySelector (
          `.box-shadow.${relatedClass}`
        );
        if (boxShadowElement) {
          boxShadowElement.classList.add ('active');
        }
      } else {
        // Si se quita la clase .active, también la removemos en .box-shadow
        const relatedClass = targetElement.id;
        const boxShadowElement = document.querySelector (
          `.box-shadow.${relatedClass}`
        );
        if (boxShadowElement) {
          boxShadowElement.classList.remove ('active');
        }
      }
    }
  });
};

// Configuramos el observer para observar cambios en los atributos
const observerOptions = {attributes: true, attributeFilter: ['class']};

// Creamos el observer y lo aplicamos a cada elemento target
const observer = new MutationObserver (observerCallback);
observerTargets.forEach (target => {
  if (target) {
    observer.observe (target, observerOptions);
  }
});

/////
document.addEventListener ('DOMContentLoaded', () => {
  const sliderContainers = ['#swiperKeyMob', '#swiperContactMob'];

  function debounce (func, wait) {
    let timeout;
    return function (...args) {
      const context = this;
      clearTimeout (timeout);
      timeout = setTimeout (() => func.apply (context, args), wait);
    };
  }

  function checkViewportAndHandleHeight () {
    if (window.innerWidth < 999) {
      sliderContainers.forEach (containerSelector => {
        const swiperWrapper = document.querySelector (
          `${containerSelector} .swiper-wrapper`
        );
        if (swiperWrapper) {
          const altura = swiperWrapper.offsetHeight;
          console.log (
            `Altura de ${containerSelector} .swiper-wrapper:`,
            altura
          );

          const swiperSlides = swiperWrapper.querySelectorAll ('.swiper-slide');
          swiperSlides.forEach (slide => {
            slide.style.height = `${altura}px`;
          });
        } else {
          console.log (
            `El elemento ${containerSelector} .swiper-wrapper no se encontró.`
          );
        }
      });
    } else {
      console.log ('Viewport mayor o igual a 999px. No se define la variable.');

      sliderContainers.forEach (containerSelector => {
        const swiperSlides = document.querySelectorAll (
          `${containerSelector} .swiper-wrapper .swiper-slide`
        );
        swiperSlides.forEach (slide => {
          slide.style.height = '';
        });
      });
    }
  }

  checkViewportAndHandleHeight ();

  window.addEventListener (
    'resize',
    debounce (checkViewportAndHandleHeight, 200)
  );
});

/////
// Función para inicializar drag and drop
function initializeDragAndDrop (dropAreaId) {
  const dropArea = document.getElementById (dropAreaId);

  // Evitar comportamientos predeterminados
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach (eventName => {
    dropArea.addEventListener (eventName, e => e.preventDefault ());
    dropArea.addEventListener (eventName, e => e.stopPropagation ());
  });

  // Añadir estilos cuando el archivo está sobre el área
  ['dragenter', 'dragover'].forEach (eventName => {
    dropArea.addEventListener (eventName, () => {
      dropArea.classList.add ('drag-over');
    });
  });

  // Remover estilos cuando el archivo sale del área
  ['dragleave', 'drop'].forEach (eventName => {
    dropArea.addEventListener (eventName, () => {
      dropArea.classList.remove ('drag-over');
    });
  });

  // Manejar la carga del archivo
  dropArea.addEventListener ('drop', event => {
    const files = event.dataTransfer.files;
    handleFiles (files, dropAreaId);
  });

  // Click para subir archivos
  dropArea.addEventListener ('click', () => {
    const input = document.createElement ('input');
    input.type = 'file';
    input.onchange = () => handleFiles (input.files, dropAreaId);
    input.click ();
  });
}

// Función para manejar los archivos
function handleFiles (files, dropAreaId) {
  if (files.length > 0) {
    const file = files[0]; // Solo procesamos el primer archivo por área
    const dropArea = document.getElementById (dropAreaId);
    dropArea.innerHTML = `<p>File uploaded: ${file.name}</p>`;
  }
}

// Inicializar las áreas
initializeDragAndDrop ('company-deck');
