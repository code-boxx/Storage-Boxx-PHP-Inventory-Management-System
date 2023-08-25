<?php
class Move extends Core {
  // (A) HELPER - GET TOTAL & BATCH QUANTITY
  //  $sku : item SKU
  //  $batch : batch name
  function getQ ($sku, $batch) {
    $qty = $this->DB->fetch(
      "SELECT `item_name`, `item_unit`, `batch_expire`, `item_low`, `batch_qty`, `item_qty`
       FROM `item_batches` b
       JOIN `items` i ON (b.`item_sku`=i.`item_sku`)
       WHERE b.`item_sku`=? AND `batch_name`=?",
       [$sku, $batch]
    );
    if ($qty == null) { return false; }
    $qty["item_low"] = (float)$qty["item_low"];
    $qty["batch_qty"] = (float)$qty["batch_qty"];
    $qty["item_qty"] = (float)$qty["item_qty"];
    return $qty;
  }

  // (B) GET BATCH
  //  $sku : item SKU
  //  $name : batch name
  function getB ($sku, $name) {
    return $this->DB->fetch(
      "SELECT * FROM `item_batches` WHERE `item_sku`=? AND `batch_name`=?",
      [$sku, $name]
    );
  }

  // (C) GET ALL BATCHES
  //  $search : item SKU, optional
  //  $page : page number, optional
  function getAllB ($search=null, $page=null) {
    // (C1) PAGINATION
    if ($page != null) {
      $sql = "SELECT COUNT(*) FROM `item_batches` b";
      $data = null;
      if ($search != null) {
        $sql .= " LEFT JOIN `items` i USING (`item_sku`) WHERE b.`item_sku` LIKE ? OR i.`item_name` LIKE ?";
        $data = ["%$search%", "%$search%"];
      }
      $this->Core->paginator($this->DB->fetchCol($sql, $data), $page);
    }

    // (C2) GET ENTRIES
    $sql = "SELECT b.*, i.`item_name`, i.`item_unit`, DATE_FORMAT(b.`batch_expire`, '".D_LONG."') `ex`
    FROM `item_batches` b
    LEFT JOIN `items` i USING (`item_sku`)";
    $data = null;
    if ($search != null) {
      $sql .= " WHERE b.`item_sku` LIKE ? OR i.`item_name` LIKE ?";
      $data = ["%$search%", "%$search%"];
    }
    $sql .= " ORDER BY `batch_date` DESC";
    if ($page != null) { $sql .= $this->Core->page["lim"]; }

    // (C3) RESULTS
    return $this->DB->fetchAll($sql, $data);
  }

  // (D) GET STOCK MOVEMENT HISTORY
  //  $sku : item SKU
  //  $batch : batch name, optional
  //  $page : page number, optional
  function getM ($sku, $batch=null, $page=null) {
    // (D1) PAGINATION
    if ($page != null) {
      $sql = "SELECT COUNT(*) FROM `item_mvt` WHERE `item_sku`=?";
      $data = [$sku];
      if ($batch!=null && $batch!="") {
        $sql .= " AND `batch_name`=?";
        $data[] = $batch;
      }
      $this->Core->paginator($this->DB->fetchCol($sql, $data), $page);
    }

    // (D2) GET ENTRIES
    $sql = "SELECT *, DATE_FORMAT(`mvt_date`, '".DT_LONG."') `md`
    FROM `item_mvt`
    WHERE `item_sku`=?";
    $data = [$sku];
    if ($batch!=null && $batch!="") {
      $sql .= " AND `batch_name`=?";
      $data[] = $batch;
    }
    $sql .= " ORDER BY `mvt_date` DESC";
    if ($page != null) { $sql .= $this->Core->page["lim"]; }

    // (D3) RESULTS
    return $this->DB->fetchAll($sql, $data);
  }

  // (E) SAVE STOCK MOVEMENT
  // NOTE: WILL AUTO-ADAPT USER NAME FROM SESSION
  // RETURNS NEW ITEM QUANTITY IF OK, FALSE IF FAIL
  //  $sku : item SKU
  //  $batch : batch name
  //  $direction : "I"n, "O"ut, "T"ake, "D"iscard
  //  $qty : quantity
  //  $notes : notes, if any
  function saveM ($sku, $batch, $direction, $qty, $notes=null) {
    // (E1) GET EXISTING QUANTITIES
    $qnow = $this->getQ($sku, $batch);
    if (!is_array($qnow)) {
      $this->error = "$sku/$batch - Invalid SKU/batch";
      return false;
    }

    // (E2) CALCULATE NEW QUANTITY
    $qty = (float)$qty;
    $qnew = [
      "batch_qty" => $qnow["batch_qty"],
      "item_qty" => $qnow["item_qty"]
    ];
    if ($direction == "I") {
      $qnew["batch_qty"] += $qty;
      $qnew["item_qty"] += $qty;
    }
    if ($direction == "O" || $direction == "D") {
      $qnew["batch_qty"] -= $qty;
      $qnew["item_qty"] -= $qty;
    }
    if ($direction == "T") {
      $delta = $qty - $qnew["batch_qty"];
      $qnew["batch_qty"] = $qty;
      $qnew["item_qty"] += $delta;
    }

    // (E3) ADD MOVEMENT & UPDATE QUANTITIES
    $this->DB->insert("item_mvt",
      ["item_sku", "batch_name", "mvt_direction", "user_name", "mvt_qty", "mvt_notes", "item_left", "batch_left"],
      [$sku, $batch, $direction, $_SESSION["user"]["user_name"], $qty, $notes, $qnew["item_qty"], $qnew["batch_qty"]]
    );
    $this->DB->update("items",
      ["item_qty"], "`item_sku`=?",
      [$qnew["item_qty"], $sku]
    );
    $this->DB->update("item_batches",
      ["batch_qty"], "`item_sku`=? AND `batch_name`=?",
      [$qnew["batch_qty"], $sku, $batch]
    );

    // (E4) SEND NOTIFICATION IF MONITORED ITEM
    if ($qnow["item_low"]!=0 && $qnew["item_qty"]<=$qnow["item_low"]) {
      $this->Core->load("Push");
      $this->Push->send("[{$sku}] {$qnow["item_name"]}",
        "Item is low on stock - {$qnew["item_qty"]} {$qnow["item_unit"]}",
        HOST_ASSETS . "favicon.png", HOST_ASSETS . "head-storage-boxx.webp"
      );
    }

    // (E5) RETURN RESULT
    return $qnew;
  }

  // (F) SAVE STOCK MOVEMENT - WITH CHECKS
  // NOTE: WILL AUTO-ADAPT USER ID FROM SESSION
  // RETURNS NEW ITEM QUANTITY IF OK, FALSE IF FAIL
  //  $sku : item SKU
  //  $batch : batch name
  //  $direction : "I"n, "O"ut, "T"ake, "D"iscard
  //  $qty : quantity
  //  $notes : notes, if any
  function saveMC ($sku, $batch, $direction, $qty, $notes=null) {
    // (F1) CHECKS
    if (!in_array($direction, ["I", "O", "T", "D"])) {
      $this->error = "Invalid direction";
      return false;
    }
    if (!is_numeric($qty)) {
      $this->error = "Invalid quantity";
      return false;
    }

    // (F2) ADD MOVEMENT
    $this->DB->start();
    $res = $this->saveM($sku, $batch, $direction, $qty, $notes);
    $this->DB->end($res===false?$res:true);
    return $res;
  }

  // (G) SAVE BATCH
  //  $sku : item SKU
  //  $name : batch name
  //  $expire : expiry date (if any)
  //  $qty : remaining stock (new batch only)
  //  $oname : old batch name (edit only)
  function saveB ($sku, $name, $expire=null, $qty=null, $oname=null) {
    // (G1) CHECK VALID SKU
    if ($this->DB->fetchCol("SELECT `item_sku` FROM `items` WHERE `item_sku`=?", [$sku]) == null) {
      $this->error = "$sku - Invalid SKU";
      return false;
    }

    // (G2) CHECK BATCH NAME - IF NEW BATCH OR CHANGING BATCH NAME
    if ($oname==null || ($oname!=null && $oname!=$name)) {
      if ($this->DB->fetchCol(
        "SELECT `batch_name` FROM `item_batches` WHERE `item_sku`=? AND `batch_name`=?",
        [$sku, $name]
      ) != null) {
        $this->error = "$name - Batch name is already in use";
        return false;
      }
    }

    // (G3) ADD BATCH
    $this->DB->start();
    if ($oname==null) {
      // (G3-1) NEW BATCH ENTRY
      // note - insert 0 quantity, add movement below will update to the correct quantity.
      $this->DB->insert("item_batches",
        ["item_sku", "batch_name", "batch_expire", "batch_qty"],
        [$sku, $name, $expire==""?null:$expire, 0]
      );

      // (G3-2) UPDATE QUANTITY & ADD MOVEMENT
      if ($qty != 0) { $this->saveM($sku, $name, "T", $qty, "Added new batch."); }
    }

    // (G4) UPDATE BATCH
    else {
      $this->DB->update("item_batches",
        ["batch_name", "batch_expire"],
        "`item_sku`=? AND `batch_name`=?",
        [$name, $expire, $sku, $oname]
      );
      if ($oname!=$name) {
        $this->DB->update("item_mvt",
          ["batch_name"],
          "`item_sku`=? AND `batch_name`=?",
          [$name, $sku, $oname]
        );
      }
    }

    // (G5) DONE
    $this->DB->end();
    return true;
  }

  // (H) DELETE BATCH
  //  $sku : item SKU
  //  $name : batch name
  function delB ($sku, $name) {
    // (H1) GET CURRENT QUANTITY
    $qnow = $this->getQ($sku, $name);

    // (H2) UPDATE TOTAL QUANTITY & DELETE BATCH
    $this->DB->start();
    $this->DB->update("items",
      ["item_qty"], "`item_sku`=?",
      [($qnow["item_qty"] - $qnow["batch_qty"]), $sku]
    );
    $this->DB->delete("item_mvt", "`item_sku`=? AND `batch_name`=?", [$sku, $name]);
    $this->DB->delete("item_batches", "`item_sku`=? AND `batch_name`=?", [$sku, $name]);
    $this->DB->end();
    return true;
  }
}