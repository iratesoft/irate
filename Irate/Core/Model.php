<?php

namespace Irate\Core;

use \PDO;
use Irate\System;

/**
 * Base model
 */
abstract class Model
{

  protected $request;
  protected $security;
  protected $db;

  public function __construct() {
    $this->db = System::$db;
    $this->request = System::$request;
    $this->security = System::$security;
  }

  public function list ($table, $filters = []) {
    $sql = "SELECT * FROM " . $table;

    if (isset($filters['order']['column']) && isset($filters['order']['direction'])) {
      $sql .= " ORDER BY " . $filters['order']['column'] . " " . strtoupper($filters['order']['direction']);
    }

    if (isset($limit)) {
      $sql .= " LIMIT " . $limit;
    }

    $statement = $this->db->client->prepare($sql);
    $statement->execute();
    $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

    return $data;
  }

  public function update($table, $data = [], $where) {
    $set = "";

    $count = 1;
    foreach ($data as $key => $value) {
      $set .= $key . " = :" . $key . ($count < count($data) ? ', ' : ' ');
      $count++;
    }

    $sql = "UPDATE " . $table . " SET " . $set . " WHERE " . $where;

    try {
      $statement = $this->db->client->prepare($sql);

      foreach ($data as $key => &$value) {
        $param = ':' . $key;

        $statement->bindParam($param, $value);
      }

      if (!$statement->execute()) {
        throw new \Exception('There was an error in your update statement.');
      }

    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }

    return true;
  }

  public function insert($table, $data = []) {
    $fields = "";
    $params = "";
    $count = 1;
    foreach ($data as $key => $value) {
      $fields .= $key . ($count < count($data) ? ', ' : '');
      $params .= ':' . $key . ($count < count($data) ? ', ' : '');
      $count++;
    }

    $sql = "INSERT INTO " . $table . "(" . $fields . ") VALUES(" . $params . ")";

    try {
      $statement = $this->db->client->prepare($sql);

      foreach ($data as $key => &$value) {
        $param = ':' . $key;
        $statement->bindParam($param, $value);
      }

      if (!$statement->execute()) {
        throw new \Exception('There was an error in your insert statement.');
      }

    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }

    return true;
  }

  public function delete($table, $where) {
    $sql = "DELETE FROM " . $table . " WHERE " . $where;

    try {
      $statement = $this->db->client->prepare($sql);

      if (!$statement->execute()) {
        throw new \Exception('There was an error in your delete statement.');
      }

    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }

    return true;
  }
}
