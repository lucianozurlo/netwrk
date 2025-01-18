document.getElementById ('form1').addEventListener ('submit', function (e) {
  e.preventDefault ();
  sendFormAjax (this);
});

document.getElementById ('form2').addEventListener ('submit', function (e) {
  e.preventDefault ();
  sendFormAjax (this);
});

function sendFormAjax (form) {
  const msgEstado = document.getElementById ('msg-estado');
  msgEstado.textContent = 'Sending...';

  const formData = new FormData (form);

  fetch (form.action, {
    method: 'POST',
    body: formData,
  })
    .then (response => response.json ())
    .then (data => {
      if (data.status === 'success') {
        msgEstado.innerHTML = `<span style="color:green">${data.message}</span>`;
        form.reset ();
      } else {
        msgEstado.innerHTML = `<span style="color:red">${data.message}</span>`;
      }
    })
    .catch (err => {
      msgEstado.innerHTML = `<span style="color:red">Error: ${err}</span>`;
    });
}
