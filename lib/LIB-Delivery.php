<?php
class Delivery extends Core {
  // (A) ADD OR UPDATE DELIVERY ORDER
  //  * ADAPTS USER FROM SESSION!
  //  $name : customer name
  //  $tel : customer tel
  //  $email : customer email
  //  $address : delivery address
  //  $date : delivery date
  //  $items : delivery items - nested array of [sku, name, unit, price, qty]
  //  $note : delivery notes
  //  $stat : delivery status
  //  $id : delivery id, update only
  function save ($name, $tel, $email, $address, $date, $items, $notes=null, $stat=0, $id=null) {
    // (A1) START & DATA
    $this->DB->start();
    $data = [$name, $tel, $email, $address, $notes, $date];

    // (A2) NEW DELIVERY ORDER
    if ($id==null) {
      $data[] = 0;
      $this->DB->insert("deliveries",
        ["d_name", "d_tel", "d_email", "d_address", "d_notes", "d_date", "d_status"],
        $data
      );
      $id = $this->DB->lastID;
    }

    // (A3) UPDATE DELIVERY ORDER
    else {
      $data[] = $stat;
      $data[] = $id;
      $this->DB->update("deliveries",
        ["d_name", "d_tel", "d_email", "d_address", "d_notes", "d_date", "d_status"],
        "`d_id`=?", $data
      );
      $this->DB->delete("deliveries_items", "`d_id`=?", [$id]);
    }

    // (A4) ADD ITEMS
    $items = json_decode($items, true);
    $data = []; $sku = [];
    foreach ($items as $i) {
      $data = array_merge($data, [$id], $i);
      $sku[] = "\"$i[0]\"";
    }
    $this->DB->insert("deliveries_items",
      ["d_id", "item_sku", "item_name", "item_unit", "item_price", "item_qty"],
      $data
    );

    // (A5) ON COMPLETE ONLY - DEDUCT STOCK
    if ($stat==1) {
      // (A5-1) GET CURRENT STOCK
      $data = [];
      $sku = implode(",", $sku);
      $sku = $this->DB->fetchKV(
        "SELECT * FROM `items` WHERE `item_sku` IN ($sku)",
        null, "item_sku", "item_qty"
      );
  
      // (A5-2) INSERT MOVEMENT
      foreach ($items as $i) {
        $sku[$i[0]] = $sku[$i[0]] - $i[4]; // new remaining quantity
        $data = array_merge($data, [
          $i[0], "O", $i[4], $sku[$i[0]], $_SESSION["user"]["user_name"], $id
        ]);
      }
      $this->DB->insert("item_mvt",
        ["item_sku", "mvt_direction", "mvt_qty", "item_left", "user_name", "d_id"],
        $data
      );

      // (A5-3) UPDATE REMAINING QUANTITY
      $data = null; $items = null;
      foreach ($sku as $s=>$qty) {
        $this->DB->update(
          "items", ["item_qty"],
          "`item_sku`=?", [$qty, $s]
        );
      }
    }

    // (A6) THE END
    $this->DB->end();
    return true;
  }

  // (B) GET DELIVERY ORDER
  //  $id : delivery id
  function get ($id) {
    // (B1) MAIN ORDER
    $d = $this->DB->fetch(
      "SELECT * FROM `deliveries` WHERE `d_id`=?", [$id]
    );
    if (!is_array($d)) {
      $this->error = "Invalid delivery";
      return false;
    }

    // (B2) ORDER ITEMS
    $this->DB->query(
      "SELECT `item_sku` s, `item_name` n, `item_unit` u, `item_price` p, `item_qty` q
       FROM `deliveries_items` WHERE `d_id`=?", [$id]
    );
    $d["items"] = [];
    while ($r = $this->DB->stmt->fetch(PDO::FETCH_NUM)) {
      $d["items"][] = $r;
    }

    // (B3) RETURN ORDER
    return $d;
  }

  // (C) GET ALL OR SEARCH DELIVERIES
  //  $search : optional, customer name
  //  $page : optional, current page number
  function getAll ($search=null, $page=null) {
    // (C1) PARITAL DELIVERIES SQL + DATA
    $sql = "FROM `deliveries`";
    $data = null;
    if ($search != null) {
      $sql .= " WHERE `d_name` LIKE ?";
      $data = ["%$search%"];
    }

    // (C2) PAGINATION
    if ($page != null) {
      $this->Core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->Core->page["lim"];
    }

    // (C3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "d_id");
  }
}