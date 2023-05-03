<?php
class Inventory extends Core {
  // (A) ADD OR EDIT ITEM
  // NOTE: WILL AUTO-ADAPT USER ID FROM SESSION
  //  $sku : item SKU
  //  $name : item name
  //  $unit : item unit
  //  $stock : initial stock level (new item only)
  //  $low : low stock quantity monitor
  //  $desc : item description
  //  $osku : old SKU, for editing only
  function save ($sku, $name, $unit, $stock=0, $low=0, $desc=null, $osku=null) {
    // (A1) CHECK SKU
    $checkSKU = $osku==null ? $sku : $osku ;
    $check = $this->get($sku);
    if ($osku==null && is_array($check) ||
       ($osku!=null && ($sku!=$osku && is_array($check)))) {
      $this->error = "$sku is already registered.";
      return false;
    }

    // (A2) AUTO COMMIT OFF
    $this->DB->start();

    // (A3) ADD ITEM
    if ($osku===null) {
      $this->DB->insert("stock", 
        ["stock_sku", "stock_name", "stock_desc", "stock_unit", "stock_low", "stock_qty"],
        [$sku, $name, $desc, $unit, $low, $stock]
      );
      $this->DB->insert("stock_mvt",
        ["stock_sku", "mvt_date", "mvt_direction", "user_id", "mvt_qty", "mvt_left", "mvt_notes"],
        [$sku, date("Y-m-d H:i:s"), "T", $this->Session->data["user"]["user_id"], $stock, $stock, "Item added to system - Initial stock."]
      );
    }

    // (A4) UPDATE ITEM
    else {
      $this->DB->update(
        "stock", ["stock_sku", "stock_name", "stock_desc", "stock_unit", "stock_low"], 
        "`stock_sku`=?", [$sku, $name, $desc, $unit, $low, $osku]
      );
      if ($sku!=$osku) {
        $this->DB->update("stock_mvt", ["stock_sku"], "`stock_sku`=?", [$sku, $osku]);
        $this->DB->update("suppliers_items", ["stock_sku"], "`stock_sku`=?", [$sku, $osku]);
      }
    }

    // (A5) COMMIT & RETURN RESULT
    $this->DB->end();
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

  // (C) GET/CHECK ITEM BY SKU
  //  $sku : item SKU
  //  $check : optional check flag
  function get ($sku, $check=false) {
    $item = $this->DB->fetch("SELECT * FROM `stock` WHERE `stock_sku`=?", [$sku]);
    return $check && $item==false ? false : $item ;
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
      $this->Core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->Core->page["lim"];
    }

    // (D3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "stock_sku");
  }

  // (E) ADD STOCK MOVEMENT
  // NOTE: WILL AUTO-ADAPT USER ID FROM SESSION
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

    // (E2) CALCULATE NEW QUANTITY
    $newqty = floatval($item["stock_qty"]);
    if ($direction == "I") { $newqty += $qty; }
    if ($direction == "O" || $direction == "D") { $newqty -= $qty; }
    if ($direction == "T") { $newqty = $qty; }

    // (E3) ADD MOVEMENT & QUANTITY
    $this->DB->start();
    $this->DB->insert("stock_mvt",
      ["stock_sku", "mvt_date", "mvt_direction", "user_id", "mvt_qty", "mvt_left", "mvt_notes"],
      [$sku, date("Y-m-d H:i:s"), $direction, $this->Session->data["user"]["user_id"], $qty, $newqty, $notes]
    );
    $this->DB->update("stock", ["stock_qty"], "`stock_sku`=?", [$newqty, $sku]);
    $this->DB->end();

    // (E4) SEND NOTIFICATION IF MONITORED ITEM
    if ($item["stock_low"]!=0 && $newqty<=$item["stock_low"]) {
      $this->Core->load("Push");
      $this->Push->send("[{$item["stock_sku"]}] {$item["stock_name"]}",
        "Item is low on stock - {$newqty} {$item["stock_unit"]}",
        HOST_ASSETS . "STORAGE-BOXX-PUSH-A.webp", HOST_ASSETS . "STORAGE-BOXX-PUSH-B.webp"
      );
    }

    // (E5) RETURN RESULT
    $item["stock_qty"] = $newqty;
    return $item;
  }

  // (F) GET STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  //  $limit : optional limit SQL (for pagination)
  function getMove ($sku, $page=null) {
    // (F1) PAGINATION
    if ($page != null) {
      $this->Core->paginator($this->DB->fetchCol(
        "SELECT COUNT(*) FROM `stock_mvt` WHERE `stock_sku`=?", [$sku]
      ), $page);
    }

    // (F2) GET ENTRIES
    $sql = "SELECT m.*, DATE_FORMAT(m.`mvt_date`, '".DT_LONG."') `md`, u.`user_name`
    FROM `stock_mvt` m
    LEFT JOIN `users` u USING (`user_id`)
    WHERE m.`stock_sku`=?
    ORDER BY `mvt_date` DESC";
    if ($page != null) { $sql .= $this->Core->page["lim"]; }

    // (F3) RESULTS
    return $this->DB->fetchAll($sql, [$sku]);
  }

  // (G) GET MONITORED ITEMS
  function getMonitor () {
    return $this->DB->fetchAll( "SELECT * FROM `stock` WHERE `stock_low`>0");
  }

  // (H) IMPORT ITEM
  function import ($sku, $name, $unit, $stock=0, $low=0, $desc=null) {
    // (H1) CHECK
    if (is_array($this->get($sku))) {
      $this->error = "$sku is already registered.";
      return false;
    }

    // (H2) SAVE
    return $this->save($sku, $name, $unit, $stock, $low, $desc);
  }
}