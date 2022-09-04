<?php
class Inventory extends Core {
  // (A) ADD OR EDIT ITEM
  //  $sku : item SKU
  //  $name : item name
  //  $unit : item unit
  //  $low : low stock quantity monitor
  //  $desc : item description
  //  $osku : old SKU, for editing only
  function save ($sku, $name, $unit, $low=0, $desc=null, $osku=null) {
    // (A1) CHECK SKU
    $checkSKU = $osku==null ? $sku : $osku ;
    $check = $this->get($sku);
    if ($osku==null && is_array($check) ||
       ($osku!=null && ($sku!=$osku && is_array($check)))) {
      $this->error = "$sku is already registered.";
      return false;
    }

    // (A2) DATA SETUP
    $fields = ["stock_sku", "stock_name", "stock_low", "stock_desc", "stock_unit"];
    $data = [$sku, $name, $low, $desc, $unit];

    // (A3) ADD ITEM
    if ($osku===null) { $this->DB->insert("stock", $fields, $data); }

    // (A4) UPDATE ITEM
    else {
      $this->DB->start();
      $data[] = $osku;
      $this->DB->update("stock", $fields, "`stock_sku`=?", $data);
      if ($sku!=$osku) {
        $this->DB->update("stock_mvt", ["stock_sku"], "`stock_sku`=?", [$sku, $osku]);
      }
      $this->DB->end();
    }

    // (A5) RETURN RESULT
    return true;
  }

  // (B) DELETE ITEM
  // WARNING : STOCK MOVEMENT WILL BE REMOVED AS WELL
  //  $sku : item SKU
  function del ($sku) {
    $this->DB->start();
    $this->DB->delete("stock", "`stock_sku`=?", [$sku]);
    $this->DB->delete("stock_mvt", "`stock_sku`=?", [$sku]);
    $this->DB->end();
    return true;
  }

  // (C) GET ITEM BY SKU
  //  $sku : item SKU
  function get ($sku) {
    return $this->DB->fetch("SELECT * FROM `stock` WHERE `stock_sku`=?", [$sku]);
  }

  // (D) GET OR SEARCH ITEMS
  //  $search : optional search term
  //  $page : optional current page
  function getAll ($search=null, $page=null) {
    // (D1) PARITAL ITEMS SQL + DATA
    $sql = "FROM `stock`";
    $data = null;
    if ($search!=null) {
      $sql .= " WHERE `stock_sku` LIKE ? OR `stock_name` LIKE ? OR `stock_desc` LIKE ?";
      $data = ["%$search%", "%$search%", "%$search%"];
    }

    // (D2) PAGINATION
    if ($page != null) {
      $this->core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->core->page["lim"];
    }

    // (D3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "stock_sku");
  }

  // (E) ADD STOCK MOVEMENT
  // NOTE: WILL AUTO-ADAPT USER ID FROM GLOBALS
  // RETURNS NEW ITEM QUANTITY IF OK, FALSE IF FAIL
  //  $sku : item SKU
  //  $direction : "I"n, "O"ut, "T"ake, "D"iscard
  //  $qty : quantity
  //  $notes : notes, if any
  function move ($sku, $direction, $qty, $notes=null) {
    // (E1) CHECKS
    if ($direction!="I" && $direction!="O" && $direction!="T" && $direction!="D") {
      $this->error = "Invalid direction";
      return false;
    }
    if (!is_numeric($qty)) {
      $this->error = "Invalid quantity";
      return false;
    }
    $item = $this->get($sku);
    if ($item===false) { return false; }
    if (!is_array($item)) {
      $this->error = "$sku - Invalid SKU";
      return false;
    }

    // (E2) ADD MOVEMENT
    global $_SESS;
    $this->DB->start();
    $this->DB->insert("stock_mvt",
      ["stock_sku", "mvt_date", "mvt_direction", "user_id", "mvt_qty", "mvt_notes"],
      [$sku, date("Y-m-d H:i:s"), $direction, $_SESS["user"]["user_id"], $qty, $notes]
    );

    // (E3) UPDATE QUANTITY
    $newqty = floatval($item["stock_qty"]);
    if ($direction == "I") { $newqty += $qty; }
    if ($direction == "O" || $direction == "D") { $newqty -= $qty; }
    if ($direction == "T") { $newqty = $qty; }
    $this->DB->update("stock", ["stock_qty"], "`stock_sku`=?", [$newqty, $sku]);

    // (E4) DONE
    $this->DB->end();
    return $newqty;
  }

  // (F) GET STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  //  $limit : optional limit SQL (for pagination)
  function getMove ($sku, $page=null) {
    // (F1) PAGINATION
    if ($page != null) {
      $this->core->paginator($this->DB->fetchCol(
        "SELECT COUNT(*) FROM `stock_mvt` WHERE `stock_sku`=?", [$sku]
      ), $page);
    }
    
    // (F2) GET ENTRIES
    $sql = "SELECT m.*, u.`user_name`
    FROM `stock_mvt` m
    LEFT JOIN `users` u USING (`user_id`)
    WHERE m.`stock_sku`=?
    ORDER BY `mvt_date` DESC";
    if ($page != null) { $sql .= $this->core->page["lim"]; }
    
    // (F3) RESULTS
    return $this->DB->fetchAll($sql, [$sku]);
  }

  // (G) GET MONITORED ITEMS
  function getMonitor () {
    return $this->DB->fetchAll( "SELECT * FROM `stock` WHERE `stock_low`>0");
  }
}