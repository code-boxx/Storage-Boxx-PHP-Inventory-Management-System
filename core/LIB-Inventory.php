<?php
class Inventory {
  // (A) COUNTALL () : COUNT TOTAL NUMBER OF ITEMS
  //  $search : optional search term
  function countAll ($search="") {
    $sql = "SELECT COUNT(*) `c` FROM `stock`";
    $cond = null;
    if ($search!="" && $search!=null) {
      $sql .= " WHERE `stock_sku` LIKE ? OR `stock_name` LIKE ? OR `stock_desc` LIKE ?";
      $cond = ["%$search%", "%$search%", "%$search%"];
    }
    $c = $this->core->fetch($sql, $cond);
    return $c['c'];
  }

  // (B) GETALL () : GET ALL ITEMS
  //  $search : optional search term
  //  $limit : optional limit SQL (for pagination)
  function getAll ($search="", $limit=true) {
    $sql = "SELECT * FROM `stock`";
    $cond = null;
    if ($search!="" && $search!=null) {
      $sql .= " WHERE `stock_sku` LIKE ? OR `stock_name` LIKE ? OR `stock_desc` LIKE ?";
      $cond = ["%$search%", "%$search%", "%$search%"];
    }
    if ($limit) { $sql .= $this->core->Page->limit(); }
    return $this->core->fetchAll($sql, $cond, "stock_sku");
  }

  // (C) GET () : GET ITEM BY SKU
  // $sku : item SKU
  function get ($sku) {
    return $this->core->fetch(
      "SELECT * FROM `stock` WHERE `stock_sku`=?",
      [$sku]
    );
  }

  // (D) SAVE () : ADD/EDIT ITEM
  //  $sku : item SKU
  //  $name : item name
  //  $unit : item unit
  //  $desc : item description
  //  $osku : old SKU, for editing only
  function save ($sku, $name, $unit, $desc=null, $osku=null) {
    // (D1) CHECK
    $checkSKU = $osku==null ? $sku : $osku ;
    $check = $this->get($sku);
    if ($osku==null && is_array($check) || 
       ($osku!=null && ($sku!=$osku && is_array($check)))) {
      $this->error = "$sku is already registered.";
      return false;
    }

    // (D2) ADD NEW
    if ($osku==null) {
      $sql = "INSERT INTO `stock` (`stock_sku`, `stock_name`, `stock_desc`, `stock_unit`) VALUES (?,?,?,?)";
      $cond = [$sku, $name, $desc, $unit];
    }

    // (D3) UPDATE
    else {
      $sql = "UPDATE `stock` SET `stock_sku`=?, `stock_name`=?, `stock_desc`=?, `stock_unit`=? WHERE `stock_sku`=?";
      $cond = [$sku, $name, $desc, $unit, $osku];
    }
    
    // (D4) GO!
    return $this->core->exec($sql, $cond);
  }
  
  // (E) DEL () : DELETE ITEM
  // WARNING : STOCK MOVEMENT WILL BE REMOVED AS WELL
  //  $sku : item SKU
  function del ($sku) {
    // (E1) REMOVE MAIN ENTRY
    $this->core->start();
    $pass = $this->core->exec("DELETE FROM `stock` WHERE `stock_sku`=?", [$sku]);
    
    // (E2) REMOVE MOVEMENT
    if ($pass) { $pass = $this->core->exec("DELETE FROM `stock_mvt` WHERE `stock_sku`=?", [$sku]); }
    
    // (E3) RESULT
    $this->core->end($pass);
    return $pass;
  }
  
  // (F) MOVE () : ADD STOCK MOVEMENT
  // NOTE: WILL AUTO-ADAPT USER ID FROM SESSION
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
    if (!isset($_SESSION['user'])) {
      $this->error = "Please sign in first";
      return false;
    }
    $item = $this->get($sku);
    if (!is_array($item)) {
      $this->error = "$sku - Invalid SKU";
      return false;
    }

    // (F2) ADD MOVEMENT
    $this->core->start();
    $pass = $this->core->exec(
      "INSERT INTO `stock_mvt` (`stock_sku`, `mvt_date`, `mvt_direction`, `user_id`, `mvt_qty`, `mvt_notes`) VALUES (?,?,?,?,?,?)",
      [$sku, date("Y-m-d H:i:s"), $direction, $_SESSION['user']['id'], $qty, $notes]
    );
    
    // (F3) UPDATE QUANTITY
    if ($pass) {
      $newqty = floatval($item['stock_qty']);
      if ($direction == "I") { $newqty += $qty; }
      if ($direction == "O") { $newqty -= $qty; }
      if ($direction == "T") { $newqty = $qty; }
      $pass = $this->core->exec(
        "UPDATE `stock` SET `stock_qty`=? WHERE `stock_sku`=?",
        [$newqty, $sku]
      );
    }

    // (F4) DONE
    $this->core->end($pass);
    return $pass ? $newqty : false ;
  }

  // (G) FINDSKU () : SEARCH FOR SKU
  //  $search : search term
  function findSKU ($search) {
    $results = [];
    $this->core->exec(
      "SELECT * FROM `stock` WHERE `stock_sku` LIKE ?", ["%$search%"]
    );
    while ($row = $this->core->stmt->fetch()) { $results[] = $row['stock_sku']; }
    return count($results)==0 ? null : $results ;
  }
  
  // (H) COUNTMOVE () : COUNT STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  function countmove ($sku) {
    $c = $this->core->fetch(
      "SELECT COUNT(*) `c` FROM `stock_mvt` WHERE `stock_sku`=?",
      [$sku]
    );
    return $c['c'];
  }
  
  // (I) GETMOVE () : GET STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  //  $limit : optional limit SQL (for pagination)
  function getMove ($sku, $limit=true) {
    $sql = "SELECT m.*, u.`user_name` FROM `stock_mvt` m LEFT JOIN `users` u USING (`user_id`) WHERE m.`stock_sku`=? ORDER BY `mvt_date` DESC";
    if ($limit) { $sql .= $this->core->Page->limit(); }
    return $this->core->fetchAll($sql, [$sku]);
  }
}