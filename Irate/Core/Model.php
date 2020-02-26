<?php

namespace Irate\Core;

use \PDO;
use Irate\System;

/**
 * Base model
 */
abstract class Model
{

  // Instance of the request class
  protected $request;

  // Instance of the security class
  protected $security;

  // Instance of the db class
  protected $db;

  // Instance of the email class
  protected $email;

  // Instance of the session class
  protected $session;

  public function __construct() {

    // Set all class instances from System
    $this->db       = System::$db;
    $this->request  = System::$request;
    $this->security = System::$security;
    $this->email    = System::$email;
    $this->session  = System::$session;

    $this->instantiate();
  }

  /**
   * Function that will run immediately after construct
   * so the actual model class doesn't need to run the constructor
   * and mess with the class var settings.
   */
  public function instantiate() {

  }

  /**
   * Lists data from a table based on table name and
   * filters provided.
   */
  public function list ($table, $filters = []) {
    $sql = "SELECT * FROM " . $table;

    // Set order query
    if (isset($filters['order']['column']) && isset($filters['order']['direction'])) {
      $sql .= " ORDER BY " . $filters['order']['column'] . " " . strtoupper($filters['order']['direction']);
    }

    // Set limit
    if (isset($limit)) {
      $sql .= " LIMIT " . $limit;
    }

    // Prepare and execute the statement.
    $statement = $this->db->client->prepare($sql);
    $statement->execute();
    $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

    // Return the data.
    return $data;
  }

  /**
   * Updates records in the database.
   * @param  string $table Table name
   * @param  array  $data  Columns and values to update
   * @param  string $where Where clause (id = 1)
   * @return boolean|array results
   */
  public function update($table, $data = [], $where = null) {
    // Iterate through data columns, create set
    $set = "";
    $count = 1;
    foreach ($data as $key => $value) {
      $set .= $key . " = :" . $key . ($count < count($data) ? ', ' : ' ');
      $count++;
    }

    // Main query
    $sql = "UPDATE " . $table . " SET " . $set . (!is_null($where) ? " WHERE " . $where : '');

    try {
      // Prepare the statement.
      $statement = $this->db->client->prepare($sql);

      // Bind parameters
      foreach ($data as $key => &$value) {
        $param = ':' . $key;
        $statement->bindParam($param, $value);
      }

      // If the statement does not execute
      if (!$statement->execute()) {
        throw new \Exception('There was an error in your update statement.');
      }

    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }

    return true;
  }

  /**
   * Inserts record into the database.
   * @param  string $table Table name
   * @param  array  $data  Columns and values
   * @return boolean status
   */
  public function insert($table, $data = []) {

    // Setup fields and params based on data array.
    $fields = "";
    $params = "";
    $count = 1;
    foreach ($data as $key => $value) {
      $fields .= $key . ($count < count($data) ? ', ' : '');
      $params .= ':' . $key . ($count < count($data) ? ', ' : '');
      $count++;
    }

    // Setup the INSERT statement.
    $sql = "INSERT INTO " . $table . "(" . $fields . ") VALUES(" . $params . ")";

    try {

      // Prepare the statement
      $statement = $this->db->client->prepare($sql);

      // Bind params based on data array.
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

  /**
   * Delets rows from the database.
   * @param  string $table Table name
   * @param  string $where Where clause
   * @return boolean
   */
  public function delete($table, $where = null) {
    // Delete from table using where clause.
    $sql = "DELETE FROM " . $table . (!is_null($where) ? " WHERE " . $where : "");

    try {
      // Prepare the statement.
      $statement = $this->db->client->prepare($sql);

      // If not able to execute, throw exception.
      if (!$statement->execute()) {
        throw new \Exception('There was an error in your delete statement.');
      }

    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }

    return true;
  }
}
