const StartServer = (event) => {
  const button = event.target;
  const port = button.dataset.port;

  ShowLoadingButton(button);

  fetch('/server/' + port + '/start', {
    method: 'POST',
  })
    .then(response => response.json())
    .then(data => {
      if (data['status'] === 'success') {

        let text = 'You can now reach your new server at: ';
        text += '<strong><i>' + serverUrl + ':' + data['data']['port'] + '</i></strong><br>';
        text += 'Your server admin token is the first entry in the token history table below.';

        ShowStopServerButton(button);
        ShowSuccessMessage(data['message'], text);
        ShowOnlineStatus(port);
        AddStartedDate(port, data);
        AddTokenHistory(port, data);
      } else {
        ShowStartServerButton(button);
        ShowErrorMessage(data['message']);
      }
    });
}

const StopServer = (event) => {
  const button = event.target;
  const port = button.dataset.port;

  ShowLoadingButton(button);

  fetch('/server/' + port + '/stop', {
    method: 'POST',
  })
    .then(response => response.json())
    .then(data => {
      if (data['status'] === 'success') {
        ShowStartServerButton(button);
        ShowSuccessMessage(data['message']);
        ShowOfflineStatus(port);
        RemoveStartedDate(port);
      } else {
        ShowStopServerButton(button);
        ShowErrorMessage(data['message']);
      }
    });
}

const ShowLoadingButton = (button) => {
  button.innerHTML = `Loading... <div class="spinner-border spinner-border-sm" role="status">
    <span class="visually-hidden">Loading...</span>
</div>`;

  button.disabled = true;

  button.classList.remove('btn-success');
  button.classList.remove('btn-danger');
  button.classList.remove('server-stop-button');
  button.classList.remove('server-start-button');

  button.classList.add('btn-primary');

  button.removeEventListener('click', StartServer);
  button.removeEventListener('click', StopServer);
}

const ShowStartServerButton = (button) => {
  button.innerHTML = 'Start Server';

  button.disabled = false;

  button.classList.remove('btn-primary');
  button.classList.remove('btn-danger');
  button.classList.remove('server-stop-button');

  button.classList.add('btn-success');
  button.classList.add('server-stop-button');

  button.removeEventListener('click', StopServer);
  button.addEventListener('click', StartServer);
}

const ShowStopServerButton = (button) => {
  button.innerHTML = 'Stop Server';

  button.disabled = false;

  button.classList.remove('btn-primary');
  button.classList.remove('btn-success');
  button.classList.remove('server-start-button');

  button.classList.add('btn-danger');
  button.classList.add('server-stop-button');

  button.removeEventListener('click', StartServer);
  button.addEventListener('click', StopServer);
}

const ShowSuccessMessage = (successMessageText, extraSuccessMessageText = '') => {
  const successMessageRow = document.querySelector('#success-message-row');
  const successMessage = successMessageRow.querySelector('#success-message');
  const extraSuccessMessage = successMessageRow.querySelector('#extra-success-message');

  extraSuccessMessage.innerHTML = '';
  extraSuccessMessage.classList.add('d-none');

  successMessage.innerText = successMessageText;

  if (extraSuccessMessageText.length > 0) {
    extraSuccessMessage.innerHTML = extraSuccessMessageText;
    extraSuccessMessage.classList.remove('d-none');
  }

  successMessageRow.classList.remove('d-none');

  document.querySelector('#error-message-row').classList.add('d-none');
}

const ShowErrorMessage = (errorMessageText, extraErrorMessageText = '') => {
  const errorMessageRow = document.querySelector('#error-message-row');
  const errorMessage = errorMessageRow.querySelector('#error-message');
  const extraErrorMessage = errorMessageRow.querySelector('#extra-error-message');

  extraErrorMessage.innerHTML = '';
  extraErrorMessage.classList.add('d-none');

  errorMessage.innerText = errorMessageText;

  if (extraErrorMessageText.length > 0) {
    extraErrorMessage.innerHTML = extraErrorMessageText;
    extraErrorMessage.classList.remove('d-none');
  }

  errorMessageRow.classList.remove('d-none');

  document.querySelector('#success-message-row').classList.add('d-none');
}

const ShowOnlineStatus = (port) => {
  const statusDataElement = document.querySelector('#status-data-' + port);
  statusDataElement.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
}

const ShowOfflineStatus = (port) => {
  const statusDataElement = document.querySelector('#status-data-' + port);
  statusDataElement.innerHTML = '<i class="bi bi-dash-circle-fill text-danger"></i>';
}

const AddStartedDate = (port, data) => {
  const startedDataElement = document.querySelector('#started-data-' + port);
  startedDataElement.innerText = data['data']['date'];
}

const RemoveStartedDate = (port) => {
  const startedDataElement = document.querySelector('#started-data-' + port);
  startedDataElement.innerText = '-';
}

const AddTokenHistory = (port, data) => {
  const tokenHistoryRow = document.querySelector('#token-history-row');
  const tokenHistoryTableBody = document.querySelector('#token-history-table-body');
  const tableRows = tokenHistoryTableBody.querySelectorAll('tr');

  const id = tableRows.length + 1;

  const newTableRow = document.createElement('tr');

  const idData = document.createElement('th');
  idData.scope = 'row';
  idData.classList.add('id-data');
  idData.textContent = '' + id;
  newTableRow.appendChild(idData);

  const portData = document.createElement('td');
  portData.classList.add('port-data');
  portData.textContent = port.toString();
  newTableRow.appendChild(portData);

  const createdData = document.createElement('td');
  createdData.classList.add('created-data');
  createdData.textContent = data['data']['date'];
  newTableRow.appendChild(createdData);

  const tokenData = document.createElement('td');
  tokenData.classList.add('token-data');
  tokenData.textContent = data['data']['token'];
  newTableRow.appendChild(tokenData);

  tokenHistoryTableBody.prepend(newTableRow);

  tokenHistoryRow.classList.remove('d-none');
}

document.querySelectorAll('.server-start-button').forEach((button) => {
  button.addEventListener('click', StartServer);
});

document.querySelectorAll('.server-stop-button').forEach((button) => {
  button.addEventListener('click', StopServer);
});

document.querySelector('#button-close-success-message-row').addEventListener('click', () => {
  document.querySelector('#success-message-row').classList.add('d-none');
  document.querySelector('#extra-success-message').classList.add('d-none');
});

document.querySelector('#button-close-error-message-row').addEventListener('click', () => {
  document.querySelector('#error-message-row').classList.add('d-none');
  document.querySelector('#extra-error-message').classList.add('d-none');
});
