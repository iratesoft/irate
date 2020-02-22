<?php

namespace Application\Models;

use Irate\System;

/**
* Example model
*/
class TodoModel extends \Irate\Core\Model {

  /**
   * Example of how to list things from a table.
   */
  public function all () {
    $data = $this->list('todo', [
      'order' => [
        'column' => 'id',
        'direction' => 'desc'
      ],
      // You can also pass the limit below.
      // 'limit' => 5
    ]);

    return $data;
  }

  /**
   * Example of using the update model method.
   */
  public function edit ($id, $data) {
    $res = $this->update('todo', $data, 'id = ' . $id);
    if (!$res) return false;
    return true;
  }

  /**
   * Example of using the insert model method.
   */
  public function add ($data) {
    $res = $this->insert('todo', $data);
    if (!$res) return false;
    return true;
  }

  /**
   * Example of using the remove model method.
   */
  public function remove($id) {
    $res = $this->delete('todo', 'id = ' . $id);
    if (!$res) return false;
    return true;
  }
}
