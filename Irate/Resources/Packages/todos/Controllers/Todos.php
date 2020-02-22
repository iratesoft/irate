<?php

namespace Application\Controllers;

use Application\Models\TodoModel;

/**
 * Home controller
 */
class Todos extends \Irate\Core\Controller {

  /**
   * Tests the view logic
   */
  protected function index() {
    // Example model instantiation, and get list of todos.
    $TodoModel = new TodoModel();
    $todos = $TodoModel->all();

    // Example of how to render a view with variables.
    return $this->view->renderTemplate('todos/index', [
      'todos' => $todos
    ]);
  }

  /**
   * List todos
   * Response Content Type: application/json
   */
  protected function list() {

    // Example model instantiation, and get list of todos.
    $TodoModel = new TodoModel();

    // Get list of todos from model.
    $todos = $TodoModel->all();

    return $this->response
      ->setContentType('json')
      ->output(['todos' => $todos]);
  }

  /**
   * Updating a Todo
   * Response Content Type: application/json
   */
  protected function update() {

    // Get the id from the route params
    $id = $this->params['id'];

    // Setup the new todo data.
    $data = [
      'name' => $this->request->post('name'),
      'status' => $this->request->post('status')
    ];

    if ($data['name'] === false) {

      // Output the data
      return $this->response
        ->setContentType('json')
        ->setStatus(400)
        ->output([
          'error' => 'Todo[name] was not provided'
        ]);
    }

    if ($data['status'] === false) {

      // Output the data
      return $this->response
        ->setContentType('json')
        ->setStatus(400)
        ->output([
          'error' => 'Todo[status] was not provided'
        ]);
    }

    // Example model instantiation, and get list of todos.
    $TodoModel = new TodoModel();

    // Try updating the todo
    $TodoModel->edit($id, $data);

    // Output the data
    return $this->response
      ->setContentType('json')
      ->output(['success' => true]);
  }

  /**
   * Adding a Todo
   * Response Content Type: application/json
   */
  protected function add() {

    // Setup the new todo data.
    $data = [
      'name' => $this->request->post('name'),
      'status' => 0
    ];

    if ($data['name'] === false) {

      // Output the data
      return $this->response
        ->setContentType('json')
        ->setStatus(400)
        ->output([
          'error' => 'Todo[name] was not provided'
        ]);
    }

    // Example model instantiation, and get list of todos.
    $TodoModel = new TodoModel();

    // Try updating the todo
    $TodoModel->add($data);

    // Output the data
    return $this->response
      ->setContentType('json')
      ->output(['action' => 'add']);
  }

  /**
   * Deleting a Todo
   * Response Content Type: application/json
   */
  protected function remove() {
    // Get the id from the route params
    $id = $this->params['id'];

    // Example model instantiation, and get list of todos.
    $TodoModel = new TodoModel();

    // Try updating the todo
    $TodoModel->remove($id);

    // Output the data
    return $this->response
      ->setContentType('json')
      ->output([
        'action' => 'remove',
        'id'     => $id
      ]);
  }
}
