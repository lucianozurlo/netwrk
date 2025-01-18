document.getElementById ('form1').addEventListener ('submit', function (e) {
  e.preventDefault ();
  sendFormAjax (this);
});

document.getElementById ('form2').addEventListener ('submit', function (e) {
  e.preventDefault ();
  sendFormAjax (this);
});

function sendFormAjax (form) {
  const msgStatus = document.getElementById ('msg-status');
  msgStatus.textContent = 'Sending...';

  const formData = new FormData (form);

  fetch (form.action, {
    method: 'POST',
    body: formData,
  })
    .then (response => response.json ())
    .then (data => {
      if (data.status === 'success') {
        msgStatus.innerHTML = `<span style="color:green">${data.message}</span>`;
        form.reset ();
      } else {
        msgStatus.innerHTML = `<span style="color:red">${data.message}</span>`;
      }
    })
    .catch (err => {
      msgStatus.innerHTML = `<span style="color:red">Error: ${err}</span>`;
    });
}
