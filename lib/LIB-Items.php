<?php
class Items extends Core {
  // (A) ADD OR EDIT ITEM
  //  $sku : item SKU
  //  $name : item name
  //  $unit : item unit
  //  $price : price per unit
  //  $low : low stock quantity monitor
  //  $desc : item description
  //  $osku : old SKU, for editing only
  function save ($sku, $name, $unit, $price=0, $low=0, $desc=null, $osku=null) {
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
      $this->DB->insert("items", 
        ["item_sku", "item_name", "item_desc", "item_unit", "item_price", "item_low"],
        [$sku, $name, $desc, $unit, $price, $low]
      );
    }

    // (A4) UPDATE ITEM
    // DANGER - CASCADE UPDATE!
    else {
      $this->DB->update(
        "items", ["item_sku", "item_name", "item_desc", "item_unit", "item_price", "item_low"], 
        "`item_sku`=?", [$sku, $name, $desc, $unit, $price, $low, $osku]
      );
      if ($sku!=$osku) {
        $this->DB->update("item_mvt", ["item_sku"], "`item_sku`=?", [$sku, $osku]);
        $this->DB->update("suppliers_items", ["item_sku"], "`item_sku`=?", [$sku, $osku]);
        $this->DB->update("deliveries_items", ["item_sku"], "`item_sku`=?", [$sku, $osku]);
        $this->DB->update("purchases_items", ["item_sku"], "`item_sku`=?", [$sku, $osku]);
      }
    }

    // (A5) COMMIT & RETURN RESULT
    $this->DB->end();
    return true;
  }

  // (B) IMPORT ITEM
  function import ($sku, $name, $unit, $price, $low=0, $desc=null) {
    // (B1) CHECK
    if (is_array($this->get($sku))) {
      $this->error = "$sku is already registered.";
      return false;
    }

    // (B2) SAVE
    return $this->save($sku, $name, $unit, $price, $low, $desc);
  }

  // (C) DELETE ITEM
  //  DANGER - CASCADE DELETE!
  //  $sku : item SKU
  function del ($sku) {
    $this->DB->start();
    $this->DB->delete("item_mvt", "`item_sku`=?", [$sku]);
    $this->DB->delete("suppliers_items", "`item_sku`=?", [$sku]);
    $this->DB->delete("deliveries_items", "`item_sku`=?", [$sku]);
    $this->DB->delete("purchases_items", "`item_sku`=?", [$sku]);
    $this->DB->delete("items", "`item_sku`=?", [$sku]);
    $this->DB->end();
    return true;
  }

  // (D) GET ITEM BY SKU
  //  $sku : item SKU
  function get ($sku) {
    return $this->DB->fetch("SELECT * FROM `items` WHERE `item_sku`=?", [$sku]);
  }

  // (E) GET OR SEARCH ITEMS
  //  $search : optional search term
  //  $page : optional current page
  function getAll ($search=null, $page=null) {
    // (E1) PARITAL ITEMS SQL + DATA
    $sql = "FROM `items`";
    $data = null;
    if ($search!=null) {
      $sql .= " WHERE `item_sku` LIKE ? OR `item_name` LIKE ? OR `item_desc` LIKE ?";
      $data = ["%$search%", "%$search%", "%$search%"];
    }

    // (E2) PAGINATION
    if ($page != null) {
      $this->Core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->Core->page["lim"];
    }

    // (E3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "item_sku");
  }

  // (F) CHECK IF VALID SKU
  //  $sku : item SKU
  function check ($sku) {
    if ($this->DB->fetchCol("SELECT `item_sku` FROM `items` WHERE `item_sku`=?", [$sku]) == null) {
      $this->error = "$sku is not a valid item.";
      return false;
    }
    return true;
  }
}