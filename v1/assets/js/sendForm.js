// Función para manejar el drag & drop
function setupDragDropArea (dragDropElement, fileInput, fileNameElement) {
  dragDropElement.addEventListener ('dragover', function (e) {
    e.preventDefault ();
    e.stopPropagation ();
    dragDropElement.classList.add ('dragover');
  });

  dragDropElement.addEventListener ('dragleave', function (e) {
    e.preventDefault ();
    e.stopPropagation ();
    dragDropElement.classList.remove ('dragover');
  });

  dragDropElement.addEventListener ('drop', function (e) {
    e.preventDefault ();
    e.stopPropagation ();
    dragDropElement.classList.remove ('dragover');
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      fileInput.files = e.dataTransfer.files;
      dragDropElement.querySelector ('.mob-no').style.display = 'none';
      dragDropElement.querySelector ('.mob-ok').style.display = 'block';
      // Mostrar el nombre del archivo
      fileNameElement.textContent = e.dataTransfer.files[0].name;
    }
  });

  // Click para abrir el selector de archivos
  dragDropElement.addEventListener ('click', function () {
    fileInput.click ();
  });

  // Cambio en el input de archivo
  fileInput.addEventListener ('change', function () {
    if (fileInput.files && fileInput.files.length > 0) {
      dragDropElement.querySelector ('.mob-no').style.display = 'none';
      dragDropElement.querySelector ('.mob-ok').style.display = 'block';
      // Mostrar el nombre del archivo
      fileNameElement.textContent = fileInput.files[0].name;
    } else {
      dragDropElement.querySelector ('.mob-no').style.display = 'block';
      dragDropElement.querySelector ('.mob-ok').style.display = 'none';
      // Limpiar el nombre del archivo
      fileNameElement.textContent = '';
    }
  });
}

// Configurar el área de drag & drop para Formulario 2
const dragDropArea = document.getElementById ('company-deck');
const fileInput = document.getElementById ('company-deck-input');
const fileNameElement = document.getElementById ('file-name');
setupDragDropArea (dragDropArea, fileInput, fileNameElement);

// Función para enviar formularios vía AJAX
function sendFormAjax (form, statusDivId) {
  const statusDiv = document.getElementById (statusDivId);
  statusDiv.textContent = 'Sending...';
  statusDiv.style.display = 'block';

  const formData = new FormData (form);

  fetch (form.action, {
    method: 'POST',
    body: formData,
  })
    .then (response => response.json ())
    .then (data => {
      if (data.status === 'success') {
        statusDiv.innerHTML = `<span style="color:green">${data.message}</span>`;
        form.reset ();

        // Resetear el área de drag & drop si es Formulario 2
        if (form.id === 'form2') {
          dragDropArea.querySelector ('.mob-no').style.display = 'block';
          dragDropArea.querySelector ('.mob-ok').style.display = 'none';
          fileNameElement.textContent = '';
        }

        // Ocultar el mensaje después de 5 segundos
        setTimeout (() => {
          statusDiv.style.display = 'none';
          statusDiv.textContent = '';
        }, 5000);
      } else {
        statusDiv.innerHTML = `<span style="color:red">${data.message}</span>`;

        // Ocultar el mensaje después de 7 segundos
        setTimeout (() => {
          statusDiv.style.display = 'none';
          statusDiv.textContent = '';
        }, 7000);
      }
    })
    .catch (error => {
      statusDiv.innerHTML = `<span style="color:red">Error: ${error}</span>`;

      // Ocultar el mensaje después de 7 segundos
      setTimeout (() => {
        statusDiv.style.display = 'none';
        statusDiv.textContent = '';
      }, 7000);
    });
}

// Asignar eventos de envío a los formularios
document.getElementById ('form1').addEventListener ('submit', function (event) {
  event.preventDefault ();
  sendFormAjax (this, 'status-message1');
});

document.getElementById ('form2').addEventListener ('submit', function (event) {
  event.preventDefault ();
  sendFormAjax (this, 'status-message2');
});
