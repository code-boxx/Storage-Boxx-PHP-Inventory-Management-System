<?php
class Inventory extends Core {
  // (A) ADD OR EDIT ITEM
  //  $sku : item SKU
  //  $name : item name
  //  $unit : item unit
  //  $desc : item description
  //  $osku : old SKU, for editing only
  function save ($sku, $name, $unit, $desc=null, $osku=null) {
    // (A1) CHECK SKU
    $checkSKU = $osku==null ? $sku : $osku ;
    $check = $this->get($sku);
    if ($check===false) { return false; }
    if ($osku==null && is_array($check) ||
    ($osku!=null && ($sku!=$osku && is_array($check)))) {
      $this->error = "$sku is already registered.";
      return false;
    }

    // (A2) DATA SETUP
    $fields = ["stock_sku", "stock_name", "stock_desc", "stock_unit"];
    $data = [$sku, $name, $desc, $unit];

    // (A3) ADD ITEM
    if ($osku===null) {
      return $this->DB->insert("stock", $fields, $data);
    }

    // (A4) UPDATE ITEM
    else {
      // UPDATE MAIN ENTRY
      $this->DB->start();
      $data[] = $osku;
      $pass = $this->DB->update("stock", $fields, "`stock_sku`=?", $data);

      // UPDATE MOVEMENT SKU (IF SKU IS CHANGED)
      if ($pass && $sku!=$osku) {
        $pass = $this->DB->update(
          "stock_mvt", ["stock_sku"], "`stock_sku`=?",
          [$sku, $osku]
        );
      }

      // RESULTS
      $this->DB->end($pass);
      return $pass;
    }
  }

  // (B) DELETE ITEM
  // WARNING : STOCK MOVEMENT WILL BE REMOVED AS WELL
  //  $sku : item SKU
  function del ($sku) {
    // (B1) REMOVE MAIN ENTRY
    $this->DB->start();
    $pass = $this->DB->query("DELETE FROM `stock` WHERE `stock_sku`=?", [$sku]);

    // (B2) REMOVE MOVEMENT
    if ($pass) { $pass = $this->DB->query("DELETE FROM `stock_mvt` WHERE `stock_sku`=?", [$sku]); }

    // (B3) RESULT
    $this->DB->end($pass);
    return $pass;
  }

  // (C) GET ITEM BY SKU
  //  $sku : item SKU
  function get ($sku) {
    return $this->DB->fetch(
      "SELECT * FROM `stock` WHERE `stock_sku`=?", [$sku]
    );
  }

  // (D) COUNT TOTAL NUMBER OF ITEMS
  //  $search : optional search term
  function count ($search=null) {
    $sql = "SELECT COUNT(*) FROM `stock`";
    $data = null;
    if ($search!=null) {
      $sql .= " WHERE `stock_sku` LIKE ? OR `stock_name` LIKE ? OR `stock_desc` LIKE ?";
      $data = ["%$search%", "%$search%", "%$search%"];
    }
    return $this->DB->fetchCol($sql, $data);
  }

  // (E) GET OR SEARCH ITEMS
  //  $search : optional search term
  //  $page : optional current page
  function getAll ($search="", $page=1) {
    // (E1) PAGINATION
    $entries = $this->count($search);
    if ($entries===false) { return false; }
    $pgn = $this->core->paginator($entries, $page);

    // (E2) GET ITEMS
    $sql = "SELECT * FROM `stock`";
    $data = null;
    if ($search!=null) {
      $sql .= " WHERE `stock_sku` LIKE ? OR `stock_name` LIKE ? OR `stock_desc` LIKE ?";
      $data = ["%$search%", "%$search%", "%$search%"];
    }
    $sql .= " LIMIT {$pgn["x"]}, {$pgn["y"]}";
    $items = $this->DB->fetchAll($sql, $data, "stock_sku");
    if ($items===false) { return false; }

    // (E3) RESULTS
    return ["data" => $items, "page" => $pgn];
  }

  // (F) ADD STOCK MOVEMENT
  // NOTE: WILL AUTO-ADAPT USER ID FROM GLOBALS
  // RETURNS NEW ITEM QUANTITY IF OK, FALSE IF FAIL
  //  $sku : item SKU
  //  $direction : "I"n, "O"ut, stock "T"ake
  //  $qty : quantity
  //  $notes : notes, if any
  function move ($sku, $direction, $qty, $notes=null) {
    // (F1) CHECKS
    if ($direction!="I" && $direction!="O" && $direction!="T") {
      $this->error = "Invalid direction";
      return false;
    }
    if (!is_numeric($qty)) {
      $this->error = "Invalid quantity";
      return false;
    }
    global $_USER;
    if ($_USER===false) {
      $this->error = "Please sign in first";
      return false;
    }
    $item = $this->get($sku);
    if ($item===false) { return false; }
    if (!is_array($item)) {
      $this->error = "$sku - Invalid SKU";
      return false;
    }

    // (F2) ADD MOVEMENT
    $this->DB->start();
    $pass = $this->DB->insert("stock_mvt",
      ["stock_sku", "mvt_date", "mvt_direction", "user_id", "mvt_qty", "mvt_notes"],
      [$sku, date("Y-m-d H:i:s"), $direction, $_USER["user_id"], $qty, $notes]
    );

    // (F3) UPDATE QUANTITY
    if ($pass) {
      $newqty = floatval($item["stock_qty"]);
      if ($direction == "I") { $newqty += $qty; }
      if ($direction == "O") { $newqty -= $qty; }
      if ($direction == "T") { $newqty = $qty; }
      $pass = $this->DB->update(
        "stock", ["stock_qty"], "`stock_sku`=?", [$newqty, $sku]
      );
    }

    // (F4) DONE
    $this->DB->end($pass);
    return $pass ? $newqty : false ;
  }

  // (G) COUNT STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  function countMove ($sku) {
    return $this->DB->fetchCol(
      "SELECT COUNT(*) FROM `stock_mvt` WHERE `stock_sku`=?",
      [$sku]
    );
  }

  // (H) GET STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  //  $limit : optional limit SQL (for pagination)
  function getMove ($sku, $page=1) {
    // (H1) PAGINATION
    $entries = $this->countMove($sku);
    if ($entries===false) { return false; }
    $pgn = $this->core->paginator($entries, $page);

    // (H2) GET ENTRIES
    $items = $this->DB->fetchAll(
      "SELECT m.*, u.`user_name`
      FROM `stock_mvt` m
      LEFT JOIN `users` u USING (`user_id`)
      WHERE m.`stock_sku`=?
      ORDER BY `mvt_date` DESC
      LIMIT {$pgn["x"]}, {$pgn["y"]}",
      [$sku]
    );
    if ($items===false) { return false; }

    // (H3) RESULTS
    return ["data" => $items, "page" => $pgn];
  }
}
