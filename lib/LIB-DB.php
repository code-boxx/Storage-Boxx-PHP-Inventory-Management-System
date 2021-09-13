<?php
class DB extends Core {
  // (A) PROPERTIES
  public $pdo = null; // PDO object
  public $stmt = null; // SQL statement

  // (B) CONSTRUCTOR - CONNECT TO DATABASE
  function __construct ($core) {
    parent::__construct($core);
    $this->pdo = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
      DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]
    );
  }

  // (C) DESTRUCTOR - CLOSE DATABASE CONNECTION
  function __destruct () {
    if ($this->stmt!==null) { $this->stmt = null; }
    if ($this->pdo!==null) { $this->pdo = null; }
  }

  // (D) AUTO-COMMIT OFF
  function start () {
    $this->pdo->beginTransaction();
  }

  // (E) COMMIT OR ROLLBACK?
  //  $pass : commit or rollback?
  function end ($pass=true) {
    if ($pass) { $this->pdo->commit(); }
    else { $this->pdo->rollBack(); }
  }

  // (F) EXECUTE SQL QUERY
  //  $sql : SQL query
  //  $data : array of parameters for query
  function query ($sql, $data=null) {
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($data);
      return true;
    } catch (Exception $ex) {
      $this->error = $ex->getMessage();
      return false;
    }
  }

  // (G) FETCH ALL (MULTIPLE ROWS)
  //  $sql : SQL query
  //  $data : array of parameters for query
  //  $key : optional, use this field as the array key
  //  * returns null if no results, false on error
  function fetchAll ($sql, $data=null, $key=null) {
    if (!$this->query($sql, $data)) { return false; }
    if ($key === null) { $results = $this->stmt->fetchAll(); }
    else {
      $results = [];
      while ($row = $this->stmt->fetch()) { $results[$row[$key]] = $row; }
    }
    return count($results)>0 ? $results : null ;
  }

  // (H) FETCH (SINGLE ROW)
  //  $sql : SQL query
  //  $data : array of parameters for query
  //  * returns null if no results, false on error
  function fetch ($sql, $data=null) {
    if (!$this->query($sql, $data)) { return false; }
    $result = $this->stmt->fetch();
    return $result==false ? null : $result ;
  }

  // (I) FETCH (SINGLE COLUMN)
  //  $sql : SQL query
  //  $data : array of parameters for query
  //  * returns null if no results, false on error
  function fetchCol ($sql, $data=null) {
    if (!$this->query($sql, $data)) { return false; }
    $result = $this->stmt->fetchColumn();
    return $result==false ? null : $result ;
  }

  // (J) INSERT OR REPLACE SQL HELPER
  //  $table : table to insert into
  //  $fields : array of fields to insert
  //  $data : data array to insert
  //  $replace : replace instead of insert?
  function insert ($table, $fields, $data, $replace=false) {
    // (J1) QUICK CHECK
    $cfields = count($fields);
    $cdata = count($data);
    $segments = $cdata / $cfields;
    if (is_float($segments)) {
      $this->error = "Number of data elements do not match with number of fields";
      return false;
    }

    // (J2) FORM SQL
    $sql = $replace ? "REPLACE" : "INSERT" ;
    $sql .= " INTO `$table` (";
    foreach ($fields as $f) { $sql .= "`$f`,"; }
    $sql = substr($sql, 0, -1).") VALUES ";
    $sql .= str_repeat("(". substr(str_repeat("?,", $cfields), 0, -1) ."),", $segments);
    $sql = substr($sql, 0, -1).";";

    // (J3) RUN QUERY
    return $this->query($sql, $data);
  }

  // (K) UPDATE SQL HELPER
  //  $table : table to update
  //  $fields : array of fields to update
  //  $where : where clause for update SQL
  //  $data : data array to update
  function update ($table, $fields, $where, $data) {
    $sql = "UPDATE `$table` SET ";
    foreach ($fields as $f) { $sql .= "`$f`=?,"; }
    $sql = substr($sql, 0, -1) . " WHERE $where";
    return $this->query($sql, $data);
  }
}
