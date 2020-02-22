class Todo {

  constructor() {
    const self = this;

    self.fetch();
  }

  fetch () {
    let self = this;

    this.list((todos) => {
      $('#todo-list').html('');

      todos.forEach((todo) => {
        $('#todo-list').append(self.template(todo));
      });
    });
  }

  list(cb) {
    // Example of how to make use of an api call.
    Irate.api('GET', '/todos/list', {}, (error, res) => {
      if (error) return alert(error);
      if (typeof cb === 'function') cb(res.todos);
    });
  }

  add () {
    const self = this;

    let data = { name: $('#todo-name').val() };
    if (!data.name) return alert('Name can not be empty.');

    // Example of how to make use of an api call.
    Irate.api('POST', '/todos/add', data, (error, res) => {
      if (error) return alert(error);
      $('#todo-name').val('');
      self.fetch();
    });
  }

  edit (el) {
    const self = this;

    let id   = el.data('id');
    let data = { name: el.val(), status: $('.todo-check[data-id="' + id + '"]').prop('checked') ? '1' : '0' };
    if (!data.name) return alert('Name can not be empty.');

    // Example of how to make use of an api call.
    Irate.api('POST', '/todos/update/' + id, data, (error, res) => {
      if (error) return alert(error);
      self.fetch();
    });
  }

  delete (id) {
    const self = this;

    // Example of how to make use of an api call.
    Irate.api('GET', '/todos/remove/' + id, {}, (error, res) => {
      if (error) return alert(error);
      self.fetch();
    });
  }

  template (todo) {
    let html = '<div class="todo-item">' +
      '<input type="checkbox" class="todo-check" data-id="' + todo.id + '" ' + (todo.status === '1' ? 'checked="checked"' : '') + ' />' +
      '<input type="text" class="todo-input" data-id="' + todo.id + '" value="' + todo.name + '" />' +
      '<i class="icon fas fa-trash todo-delete" data-id="' + todo.id + '"></i>' +
      '<div class="clearfix"></div>' +
    '</div>';

    return html;
  }
}

window.addEventListener('DOMContentLoaded', (event) => {
  const Todos = new Todo();

  $('body').on('keypress', '#todo-name', function (event) {
      let keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13') Todos.add();
  });

  $('body').on('click', '#todo-add', () => Todos.add());

  $('body').on('keypress', '.todo-input', function (event) {
    let keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13') Todos.edit($(event.target));
  });

  $('body').on('change', '.todo-check', function (event) {
    let id = $(event.target).data('id');
    console.log('Test')
    Todos.edit($('.todo-input[data-id="' + id + '"]'));
  });

  $('body').on('click', '.todo-delete', function () {
    let id = $(this).data('id');
    Todos.delete(id);
  });
});
