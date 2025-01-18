// Attach event listeners
document.getElementById ('form1').addEventListener ('submit', function (e) {
  e.preventDefault ();
  sendFormAjax (this, 'status-message1');
});

document.getElementById ('form2').addEventListener ('submit', function (e) {
  e.preventDefault ();
  sendFormAjax (this, 'status-message2');
});

// We pass the ID of the status message container
function sendFormAjax (form, statusDivId) {
  const statusDiv = document.getElementById (statusDivId);
  statusDiv.textContent = 'Sending...';

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
      } else {
        statusDiv.innerHTML = `<span style="color:red">${data.message}</span>`;
      }
    })
    .catch (error => {
      statusDiv.innerHTML = `<span style="color:red">Error: ${error}</span>`;
    });
}
