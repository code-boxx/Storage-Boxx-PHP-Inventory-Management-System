<?php
class Customers extends Core {
  // (A) ADD OR UPDATE CUSTOMER
  //  $name : customer name
  //  $tel : customer telephone
  //  $email : customer email
  //  $addr : customer address
  //  $id : customer id (for updating only)
  function save ($name, $tel, $email, $addr=null, $id=null) {
    // (A1) DATA FIELDS
    $fields = ["cus_name", "cus_tel", "cus_email", "cus_address"];
    $data = [$name, $tel, $email, $addr];

    // (A2) ADD/UPDATE CUSTOMER
    if ($id===null) {
      $this->DB->insert("customers", $fields, $data);
    } else {
      $data[] = $id;
      $this->DB->update("customers", $fields, "`cus_id`=?", $data);
    }
    return true;
  }

  // (B) DELETE CUSTOMER
  //  DANGER - CASCADE DELETE!
  //  $id : customer id
  function del ($id) {
    $this->DB->start();
    $this->DB->query(
      "DELETE `item_mvt`
       FROM `item_mvt`
       LEFT JOIN `deliveries` USING (`d_id`) 
       WHERE `cus_id`=?", [$id]
    );
    $this->DB->query(
      "DELETE `deliveries`, `deliveries_items`
       FROM `deliveries_items`
       LEFT JOIN `deliveries` USING (`d_id`) 
       WHERE `cus_id`=?", [$id]
    );
    $this->DB->delete("customers", "`cus_id`=?", [$id]);
    $this->DB->end();
    return true;
  }

  // (C) GET CUSTOMER
  //  $id : customer id or email
  function get ($id) {
    return $this->DB->fetch(
      "SELECT * FROM `customers` WHERE `cus_". (is_numeric($id)?"id":"email") ."`=?",
      [$id]
    );
  }

  // (D) GET ALL OR SEARCH CUSTOMERS
  //  $search : optional, customer name or email
  //  $page : optional, current page number
  function getAll ($search=null, $page=null) {
    // (D1) PARITAL CUSTOMERS SQL + DATA
    $sql = "FROM `customers`";
    $data = null;
    if ($search != null) {
      $sql .= " WHERE `cus_name` LIKE ? OR `cus_email` LIKE ?";
      $data = ["%$search%", "%$search%"];
    }

    // (D2) PAGINATION
    if ($page != null) {
      $this->Core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->Core->page["lim"];
    }

    // (D3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "cus_id");
  }

  // (E) IMPORT CUSTOMERS
  //  $name : customer name
  //  $tel : customer telephone
  //  $email : customer email
  //  $addr : customer address
  function import ($name, $tel, $email, $addr=null) {
    // (E1) CHECK EMAIL
    if (is_array($this->get($email))) {
      $this->error = "$email is already registered";
      return false;
    }

    // (E2) SAVE CUSTOMER
    $this->save($name, $tel, $email, $addr);
    return true;
  }
}