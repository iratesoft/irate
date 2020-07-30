class Irate {

  static api(method, route, data, cb) {
    $.ajax({
      type: method,
      url: (window.BASE_URL !== '/' ? window.BASE_URL : '') + route,
      dataType: "json",
      data: data,
      async: true
    }).done((res) => {
      cb(false, res);
    }).fail((err) => {
      var error = $.parseJSON(err).error;
      cb(error);
    });
  }
}
