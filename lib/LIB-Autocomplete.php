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
  function item ($search) {
    return $this->query(
      "SELECT * FROM `items` WHERE `item_name` LIKE ?",
      ["%$search%"], "item_name"
    );
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

  // (G) SUGGEST BATCH NAME
  function batch ($search, $sku) {
    // (G1) NO SKU SPECIFIED
    if ($sku=="" || $sku==null) { return []; }

    // (G2) SEARCH
    return $this->query(
      "SELECT * FROM `item_batches` WHERE `item_sku`=? AND `batch_name` LIKE ?",
      [$sku, "%$search%"], "batch_name"
    );
  }
}