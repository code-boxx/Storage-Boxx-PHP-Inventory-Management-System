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
  //  $search : supplier name
  //  $more : include more supplier information?
  function sup ($search, $more=false) {
    // (B1) MORE - INCLUDE SUPPLIER INFO
    if ($more) {
      $res = [];
      $this->DB->query(
        "SELECT `sup_id` i, `sup_name` n, `sup_tel` t, `sup_email` e, `sup_address` a
        FROM `suppliers` WHERE `sup_name` LIKE ?
        LIMIT " . SUGGEST_LIMIT, ["%$search%"]
      );
      $res = [];
      while ($r = $this->DB->stmt->fetch()) {
        $res[] = ["n" => $r["n"], "v" => json_encode($r)];
      }
      return $res;
    }

    // (B2) "SIMPLE" - SUPPLIER NAME ONLY
    else {
      return $this->query(
        "SELECT * FROM `suppliers` WHERE `sup_name` LIKE ?",
        ["%$search%"], "sup_name"
      );
    }
  }

  // (C) SUGGEST SUPPLIER ITEM
  //  $search : item name
  //  $sid : supplier id
  //  $more : include more item information
  function supitem ($search, $sid, $more=false) {
    // (C1) "MORE" - RETURN ALL ITEM DATA
    if ($more) {
      $res = [];
      $this->DB->query(
        "SELECT i.`item_sku` s, i.`item_name` n, i.`item_unit` u, s.`unit_price` p
         FROM `suppliers_items` s
         LEFT JOIN `items` i USING (`item_sku`)
         WHERE (i.`item_sku` LIKE ? OR i.`item_name` LIKE ? OR s.`sup_sku` LIKE ?) AND s.`sup_id`=?
         LIMIT " . SUGGEST_LIMIT,
        ["%$search%", "%$search%", "%$search%", $sid]
      );
      while ($r = $this->DB->stmt->fetch()) {
        $res[] = [
          "n" => sprintf("[%s] %s", $r["s"], $r["n"]),
          "v" => json_encode($r)
        ];
      }
      return $res;
    }

    // (C2) "SIMPLE" - RETURN ITEM NAME ONLY
    else {
      return $this->query(
        "SELECT * FROM `suppliers_items` s
         LEFT JOIN `items` i USING (`item_sku`)
         WHERE (i.`item_sku` LIKE ? OR i.`item_name` LIKE ? OR s.`sup_sku` LIKE ?) AND s.`sup_id`=?",
        ["%$search%", "%$search%", "%$search%", $sid], "item_name"
      );
    }
  }

  // (D) SUGGEST USER  
  //  $search : user name/email
  function user ($search) {
    return $this->query(
      "SELECT * FROM `users` WHERE `user_name` LIKE ? OR `user_email` LIKE ?",
      ["%$search%", "%$search%"], "user_name"
    );
  }

  // (E) SUGGEST ITEM  
  //  $search : item sku/name
  //  $more : include more item information
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
  //  $search : item name/sku
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
  //  $search : customer name
  //  $more : include more customer info?
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
  //  $search : deliver to
  function deliver ($search) {
    return $this->query(
      "SELECT * FROM `deliveries` WHERE `d_name` LIKE ?",
      ["%$search%"], "d_name"
    );
  }

  // (I) SUGGEST PURCHASE SUPPLIER
  //  $search : supplier name
  function purchase ($search) {
    return $this->query(
      "SELECT * FROM `purchases` LEFT JOIN `suppliers` USING (`sup_id`) WHERE `sup_name` LIKE ?",
      ["%$search%"], "sup_name"
    );
  }
}