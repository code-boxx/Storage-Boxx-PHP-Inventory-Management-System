<?php
class Move extends Core {
  // (A) HELPER - GET TOTAL QUANTITY
  //  $sku : item SKU
  function getQ ($sku) {
    $qty = $this->DB->fetch(
      "SELECT `item_name`, `item_unit`, `item_low`, `item_qty` FROM `items` WHERE `item_sku`=?",
      [$sku]
    );
    if ($qty == null) { return false; }
    $qty["item_low"] = (float)$qty["item_low"];
    $qty["item_qty"] = (float)$qty["item_qty"];
    return $qty;
  }

  // (B) GET STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  //  $page : page number, optional
  function getM ($sku, $page=null) {
    // (B1) PAGINATION
    if ($page != null) {
      $sql = "SELECT COUNT(*) FROM `item_mvt` WHERE `item_sku`=?";
      $data = [$sku];
      $this->Core->paginator($this->DB->fetchCol($sql, $data), $page);
    }
    
    // (B2) GET ENTRIES
    $sql = "SELECT *, DATE_FORMAT(`mvt_date`, '".DT_LONG."') `md`
    FROM `item_mvt`
    WHERE `item_sku`=?";
    $data = [$sku];
    $sql .= " ORDER BY `mvt_date` DESC";
    if ($page != null) { $sql .= $this->Core->page["lim"]; }

    // (B3) RESULTS
    return $this->DB->fetchAll($sql, $data);
  }

  // (C) SAVE STOCK MOVEMENT
  // NOTE: WILL AUTO-ADAPT USER NAME FROM SESSION
  // RETURNS NEW ITEM QUANTITY IF OK, FALSE IF FAIL
  //  $sku : item SKU
  //  $direction : "I"n, "O"ut, "T"ake, "D"iscard
  //  $qty : quantity
  //  $notes : notes, if any
  function saveM ($sku, $direction, $qty, $notes=null) {
    // (C1) GET EXISTING QUANTITIES
    $qnow = $this->getQ($sku);
    if (!is_array($qnow)) {
      $this->error = "$sku - Invalid SKU";
      return false;
    }
    
    // (C2) CALCULATE NEW QUANTITY
    $qty = (float)$qty;
    $qnew = $qnow["item_qty"];
    if ($direction == "I") { $qnew += $qty; }
    if ($direction == "O" || $direction == "D") { $qnew -= $qty; }
    if ($direction == "T") { $qnew = $qty; }

    // (C3) ADD MOVEMENT & UPDATE QUANTITIES
    $this->DB->insert("item_mvt",
      ["item_sku", "mvt_direction", "user_name", "mvt_qty", "mvt_notes", "item_left"],
      [$sku, $direction, $_SESSION["user"]["user_name"], $qty, $notes, $qnew]
    );
    $this->DB->update("items",
      ["item_qty"], "`item_sku`=?",
      [$qnew, $sku]
    );

    // (C4) SEND NOTIFICATION IF MONITORED ITEM
    if ($qnow["item_low"]!=0 && $qnew<=$qnow["item_low"]) {
      $this->Core->load("Push");
      $this->Push->send("[{$sku}] {$qnow["item_name"]}",
        "Item is low on stock - {$qnew} {$qnow["item_unit"]}",
        HOST_ASSETS . "favicon.png", HOST_ASSETS . "banner.webp"
      );
    }

    // (C5) RETURN RESULT
    return $qnew;
  }

  // (D) SAVE STOCK MOVEMENT - WITH CHECKS
  // NOTE: WILL AUTO-ADAPT USER ID FROM SESSION
  // RETURNS NEW ITEM QUANTITY IF OK, FALSE IF FAIL
  //  $sku : item SKU
  //  $direction : "I"n, "O"ut, "T"ake, "D"iscard
  //  $qty : quantity
  //  $notes : notes, if any
  function saveMC ($sku, $direction, $qty, $notes=null) {
    // (D1) CHECKS
    if (!in_array($direction, ["I", "O", "T", "D"])) {
      $this->error = "Invalid direction";
      return false;
    }
    if (!is_numeric($qty)) {
      $this->error = "Invalid quantity";
      return false;
    }

    // (D2) ADD MOVEMENT
    $this->DB->start();
    $res = $this->saveM($sku, $direction, $qty, $notes);
    $this->DB->end($res===false?false:true);
    return $res;
  }
}