<?php
class Suppliers extends Core {
  // (A) ADD OR UPDATE SUPPLIER
  //  $name : supplier name
  //  $tel : supplier telephone
  //  $email : supplier email
  //  $addr : supplier address
  //  $id : supplier id (for updating only)
  function save ($name, $tel, $email, $addr=null, $id=null) {
    // (A1) DATA FIELDS
    $fields = ["sup_name", "sup_tel", "sup_email", "sup_address"];
    $data = [$name, $tel, $email, $addr];

    // (A2) ADD/UPDATE SUPPLIER
    if ($id===null) {
      $this->DB->insert("suppliers", $fields, $data);
    } else {
      $data[] = $id;
      $this->DB->update("suppliers", $fields, "`sup_id`=?", $data);
    }
    return true;
  }

  // (B) DELETE SUPPLIER
  //  $id : supplier id
  function del ($id) {
    $this->DB->start();
    $this->DB->delete("suppliers", "`sup_id`=?", [$id]);
    $this->DB->delete("suppliers_items", "`sup_id`=?", [$id]);
    $this->DB->end();
    return true;
  }

  // (C) GET SUPPLIER
  //  $id : supplier id or email
  function get ($id) {
    return $this->DB->fetch(
      "SELECT * FROM `suppliers` WHERE `sup_". (is_numeric($id)?"id":"email") ."`=?",
      [$id]
    );
  }

  // (D) GET ALL OR SEARCH SUPPLIERS
  //  $search : optional, supplier name or email
  //  $page : optional, current page number
  function getAll ($search=null, $page=null) {
    // (D1) PARITAL SUPPLIERS SQL + DATA
    $sql = "FROM `suppliers`";
    $data = null;
    if ($search != null) {
      $sql .= " WHERE `sup_name` LIKE ? OR `sup_email` LIKE ?";
      $data = ["%$search%", "%$search%"];
    }

    // (D2) PAGINATION
    if ($page != null) {
      $this->core->paginator(
        $this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page
      );
      $sql .= $this->core->page["lim"];
    }

    // (D3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "sup_id");
  }

  // (E) IMPORT SUPPLIER
  //  $name : supplier name
  //  $tel : supplier telephone
  //  $email : supplier email
  //  $addr : supplier address
  function import ($name, $tel, $email, $addr=null) {
    // (E1) CHECK EMAIL
    if (is_array($this->get($email))) {
      $this->error = "$email is already registered";
      return false;
    }

    // (E2) SAVE SUPPLIER
    $this->save($name, $tel, $email, $addr);
    return true;
  }

  // (F) SAVE ITEM TO SUPPLIER
  //  $id : supplier id
  //  $sku : item sku
  //  $ssku : supplier sku
  //  $price : unit price
  //  $osku : old SKU, for editing only
  function saveItem ($id, $sku, $ssku, $price, $osku=null) {
    // (F1) CHECKS
    if (!is_array($this->DB->fetch("SELECT * FROM `stock` WHERE `stock_sku`=?", [$sku]))) {
      $this->error = "$sku is not a valid item";
      return false;
    }
    if ($osku===null || ($osku!=null && $osku!=$sku)) {
      if (is_array($this->getItem($id, $sku))) {
        $this->error = "$sku is already registered.";
        return false;
      }
    }

    // (F2) ADD ITEM
    if ($osku===null) {
      $this->DB->insert("suppliers_items", 
        ["sup_id", "stock_sku", "sup_sku", "unit_price"],
        [$id, $sku, $ssku, $price]
      );
    }

    // (F3) UPDATE ITEM
    else {
      $this->DB->update(
        "suppliers_items", ["stock_sku", "sup_sku", "unit_price"], 
        "`sup_id`=? AND `stock_sku`=?", [$sku, $ssku, $price, $id, $osku]
      );
    }

    // (F4) RETURN RESULT
    return true;
  }

  // (G) DELETE ITEM FROM SUPPLIER
  //  $id : supplier id
  //  $sku : item sku
  function delItem ($id, $sku) {
    $this->DB->delete("suppliers_items", "`sup_id`=? AND `stock_sku`=?", [$id, $sku]);
    return true;
  }

  // (H) GET SUPPLIER ITEMS
  //  $id : supplier id
  //  $search : optional, item name
  //  $page : optional, current page number
  function getItems ($id, $search=null, $page=null) {
    // (H1) PARITAL SUPPLIERS SQL + DATA
    $sql = "FROM `suppliers_items` i JOIN `stock` s USING (`stock_sku`) WHERE i.`sup_id`=?";
    $data = [$id];
    if ($search != null) {
      $sql .= " AND s.`stock_sku` LIKE ? OR s.`stock_name` LIKE ? OR s.`stock_desc` LIKE ?";
      array_push($data, "%$search%", "%$search%", "%$search%");
    }

    // (H2) PAGINATION
    if ($page != null) {
      $this->core->paginator($this->DB->fetchCol("SELECT COUNT(*) $sql", $data), $page);
      $sql .= $this->core->page["lim"];
    }

    // (H3) RESULTS
    return $this->DB->fetchAll("SELECT * $sql", $data, "stock_sku");
  }

  // (I) GET SUPPLIER ITEM
  //  $id : supplier id
  //  $sku : item sku
  function getItem ($id, $sku) {
    return $this->DB->fetch(
      "SELECT * FROM `suppliers_items` WHERE `sup_id`=? AND `stock_sku`=?",
      [$id, $sku]
    );
  }

  // (J) IMPORT SUPPLIER ITEM
  //  $id : supplier id
  //  $sku : item sku
  //  $ssku : supplier sku
  //  $price : unit price
  function importItem ($id, $sku, $ssku, $price) {
    // (J1) CHECK VALID SKU
    if (!is_array($this->DB->fetch("SELECT * FROM `stock` WHERE `stock_sku`=?", [$sku]))) {
      $this->error = "$sku is not a valid item";
      return false;
    }

    // (J2) REPLACE
    $this->DB->insert("suppliers_items", 
      ["sup_id", "stock_sku", "sup_sku", "unit_price"],
      [$id, $sku, ($ssku==""||$ssku==null?$sku:$ssku), $price], true
    );
    return true;
  }
}