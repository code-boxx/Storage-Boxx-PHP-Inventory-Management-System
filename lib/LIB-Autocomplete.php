<?php
class Autocomplete extends Core {
  // (A) HELPER - RUN SQL SEARCH & FORMAT RESULTS
  //  $sql : sql query string
  //  $data : parameters
  //  $n : set this column as name
  //  $v : set this column as value (optional)
  function query ($sql, $data, $n, $v=null) {
    $this->DB->query($sql . " LIMIT " . SUGGEST_LIMIT, $data);
    $res = [];
    if ($v==null) {
      while ($r = $this->DB->stmt->fetch()) {
        $res[] = ["n" => $r[$n]];
      }
    } else {
      while ($r = $this->DB->stmt->fetch()) {
        $res[] = ["n" => $r[$n], "v" => $r[$v]];
      }
    }
    return $res;
  }

  // (B) SUGGEST SUPPLIER
  function sup ($search) {
    return $this->query(
      "SELECT * FROM `suppliers` WHERE `sup_name` LIKE ?",
      ["%$search%"], "sup_name"
    );
  }

  // (C) SUGGEST SUPPLIER ITEM
  function supitem ($search) {
    return $this->query(
      "SELECT * FROM `suppliers_items` s
       LEFT JOIN `items` i USING (`item_sku`)
       WHERE i.`item_name` LIKE ?",
      ["%$search%"], "item_name"
    );
  }

  // (D) SUGGEST USER  
  function user ($search) {
    return $this->query(
      "SELECT * FROM `users` WHERE `user_name` LIKE ?",
      ["%$search%"], "user_name"
    );
  }

  // (E) SUGGEST ITEM  
  function item ($search, $more=false) {
    // (E1) "MORE" - RETURN ALL ITEM DATA
    if ($more) {
      $res = [];
      $this->DB->query(
        "SELECT `item_sku` s, `item_name` n, `item_unit` u, `item_price` p
        FROM `items` WHERE `item_name` LIKE ? OR `item_sku` LIKE ?
        LIMIT " . SUGGEST_LIMIT, ["%$search%", "%$search%"]
      );
      $res = [];
      while ($r = $this->DB->stmt->fetch()) {
        $res[] = [
          "n" => sprintf("[%s] %s", $r["s"], $r["n"]),
          "v" => json_encode($r)
        ];
      }
      return $res;
    }
    
    // (E2) "SIMPLE" - RETURN ITEM NAME ONLY
    else {
      return $this->query(
        "SELECT * FROM `items` WHERE `item_name` LIKE ? OR `item_sku` LIKE ?",
        ["%$search%", "%$search%"], "item_name"
      );
    }
  }

  // (F) SUGGEST ITEM NAME/SKU
  function sku ($search) {
    $this->DB->query(
      "SELECT * FROM `items`
       WHERE `item_name` LIKE ? OR `item_sku` LIKE ?
       LIMIT " . SUGGEST_LIMIT,
      ["%$search%", "%$search%"]
    );
    $res = [];
    while ($r = $this->DB->stmt->fetch()) {
      $res[] = [
        "n" => $r["item_sku"] . " - " . $r["item_name"],
        "v" => $r["item_sku"]
      ];
    }
    return $res;
  }

  // (G) SUGGEST CUSTOMER
  function cus ($search, $more=false) {
    // (G1) "MORE" - RETURN ALL CUSTOMER DATA
    if ($more) {
      $res = [];
      $this->DB->query(
        "SELECT `cus_id` i, `cus_name` n, `cus_tel` t, `cus_email` e, `cus_address` a
        FROM `customers` WHERE `cus_name` LIKE ?
        LIMIT " . SUGGEST_LIMIT, ["%$search%"]
      );
      $res = [];
      while ($r = $this->DB->stmt->fetch()) {
        $res[] = ["n" => $r["n"], "v" => json_encode($r)];
      }
      return $res;
    }
    
    // (G2) "SIMPLE" - RETURN NAME ONLY
    else {
      return $this->query(
        "SELECT * FROM `customers` WHERE `cus_name` LIKE ?",
        ["%$search%"], "cus_name"
      );
    }
  }

  // (H) SUGGEST DELIVERY CUSTOMER
  function deliver ($search) {
    return $this->query(
      "SELECT * FROM `deliveries` WHERE `d_name` LIKE ?",
      ["%$search%"], "d_name"
    );
  }
}