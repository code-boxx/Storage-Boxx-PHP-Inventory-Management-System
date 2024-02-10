<?php
class Purchase extends Core {
  // (A) ADD OR UPDATE PURCHASE ORDER
  //  * ADAPTS USER FROM SESSION!
  //  $sid : supplier id
  //  $name : ship to name (company name)
  //  $tel : ship to tel (company tel)
  //  $email : ship to email (company email)
  //  $address : ship to address (company address)
  //  $date : purchase date
  //  $items : purchase items - nested array of [sku, name, unit, price, qty]
  //  $note : purchase notes
  //  $stat : purchase status
  //  $id : purchase order id, update only
  function save ($sid, $name, $tel, $email, $address, $date, $items, $notes=null, $stat=0, $id=null) {
    // (A1) START & DATA
    $this->DB->start();
    $data = [$sid, $name, $tel, $email, $address, $notes, $date];

    // (A2) NEW PURCHASE ORDER
    if ($id==null) {
      $data[] = 0;
      $this->DB->insert("purchases",
        ["sup_id", "p_name", "p_tel", "p_email", "p_address", "p_notes", "p_date", "p_status"],
        $data
      );
      $id = $this->DB->lastID;
    }

    // (A3) UPDATE PURCHASE ORDER
    else {
      $data[] = $stat;
      $data[] = $id;
      $this->DB->update("purchases",
        ["sup_id", "p_name", "p_tel", "p_email", "p_address", "p_notes", "p_date", "p_status"],
        "`p_id`=?", $data
      );
      $this->DB->delete("purchases_items", "`p_id`=?", [$id]);
    }

    // (A4) ADD ITEMS
    $items = json_decode($items, true);
    $data = []; $sku = []; $n = 0;
    foreach ($items as $i) {
      $data = array_merge($data, [$id, $n], $i);
      $sku[] = "\"$i[0]\"";
      $n++;
    }
    $this->DB->insert("purchases_items",
      ["p_id", "item_sort", "item_sku", "item_price", "item_qty"],
      $data
    );

    // (A5) ON COMPLETE ONLY - ADD STOCK
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
        $sku[$i[0]] = $sku[$i[0]] + $i[2]; // new remaining quantity
        $data = array_merge($data, [
          $i[0], "I", $i[2], $sku[$i[0]], $_SESSION["user"]["user_name"], $id
        ]);
      }
      $this->DB->insert("item_mvt",
        ["item_sku", "mvt_direction", "mvt_qty", "item_left", "user_name", "p_id"],
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

  // (B) GET PURCHASE ORDER
  //  $id : purchase order id
  function get ($id) {
    // (B1) MAIN ORDER
    $d = $this->DB->fetch(
      "SELECT p.*, s.`sup_name`, s.`sup_tel`, s.`sup_email`, s.`sup_address`
       FROM `purchases` p
       LEFT JOIN `suppliers` s USING (`sup_id`)
       WHERE `p_id`=?", [$id]
    );
    if (!is_array($d)) {
      $this->error = "Invalid purchase order";
      return false;
    }

    // (B2) ORDER ITEMS
    $this->DB->query(
      "SELECT p.`item_sku` s, s.`sup_sku` ss, i.`item_name` n, i.`item_unit` u, p.`item_price` p, p.`item_qty` q
       FROM `purchases_items` p
       LEFT JOIN `items` i USING (`item_sku`)
       LEFT JOIN `suppliers_items` s USING (`item_sku`)
       WHERE `p_id`=?
       ORDER BY `item_sort`", [$id]
    );
    $d["items"] = [];
    while ($r = $this->DB->stmt->fetch(PDO::FETCH_NUM)) {
      $d["items"][] = $r;
    }

    // (B3) RETURN ORDER
    return $d;
  }

  // (C) GET ALL OR SEARCH PURCHASE ORDERS
  //  $search : optional, supplier name
  //  $page : optional, current page number
  function getAll ($search=null, $page=null) {
    // (C1) PARITAL PURCHASES SQL + DATA
    $sql = "FROM `purchases` p LEFT JOIN `suppliers` s USING (`sup_id`)";
    $data = null;
    if ($search != null) {
      $sql .= " WHERE `sup_name` LIKE ?";
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
    return $this->DB->fetchAll("SELECT p.*, s.`sup_name` $sql", $data, "p_id");
  }
}