// Attach event listeners to both forms
document.getElementById ('form1').addEventListener ('submit', function (event) {
  event.preventDefault ();
  sendFormAjax (this, 'status-message1');
});

document.getElementById ('form2').addEventListener ('submit', function (event) {
  event.preventDefault ();
  sendFormAjax (this, 'status-message2');
});

/**
       * Sends form data via AJAX and handles the response.
       * @param {HTMLFormElement} form - The form element being submitted.
       * @param {string} statusDivId - The ID of the div where the status message will be displayed.
       */
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

        // Hide the message after 5 seconds (5000 milliseconds)
        setTimeout (() => {
          statusDiv.style.display = 'none';
          statusDiv.textContent = '';
        }, 5000);
      } else {
        statusDiv.innerHTML = `<span style="color:red">${data.message}</span>`;

        // Optionally, hide error messages after some time
        setTimeout (() => {
          statusDiv.style.display = 'none';
          statusDiv.textContent = '';
        }, 7000);
      }
    })
    .catch (error => {
      statusDiv.innerHTML = `<span style="color:red">Error: ${error}</span>`;

      // Optionally, hide error messages after some time
      setTimeout (() => {
        statusDiv.style.display = 'none';
        statusDiv.textContent = '';
      }, 7000);
    });
}
