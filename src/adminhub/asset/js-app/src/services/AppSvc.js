
function AppSvc() {


  this.capitalize = (string) => {
    if (typeof string === 'string' || string instanceof String) return string.charAt(0).toUpperCase() + string.slice(1);
    else return string;
  };

  this.refreshPage =  () => {
    window.location = window.location.href;
  }

  this.setProgressMessage = (message) => {
    return "<i class='fa fa-spin fa-circle-o-notch text-warning'></i> "+ this.capitalize(message);
  }

  this.setErrorMessage = (message) => {
    return "<i class='fa fa-exclamation-triangle text-danger'></i> "+this.capitalize(message);
  }

  this.setSuccessMessage = (message) => {
    return "<i class='fa fa-check text-success'></i> "+this.capitalize(message);
  }

  this.extractFormData = (formId) => {
    data = {};
    data = $('#'+formId).serializeArray().reduce(function(obj, item) {
      data[item.name] = item.value;
      return data;
    }, {});

    data['timestamp'] = Date.now() || Date.getTime();

    return JSON.stringify(data);
  };

  this.getElementDataUrl = (item) => {
    return item.getAttribute('data-url');
  };

  this.disableButtonElement = (item) => {
    item.setAttribute('disabled', 'disabled');
  };



  this.enableButtonElement = (item) => {
    item.removeAttribute('disabled');
  };

}
