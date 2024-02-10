<?php
class Report extends Core {
  // (A) HELPER - GENERATE HTML TEMPLATE
  function htop ($load=null) {
    echo "<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.5'><style>* { font-family: Arial, sans-serif; box-sizing: border-box; }</style>";
    if (is_array($load)) { foreach ($load as $l) {
      if ($l[0]=="s") {
        printf("<script src='%s'%s></script>", $l[1], isset($l[2]) ? " ".$l[2] : "");
      } else {
        printf("<link rel='stylesheet' href='%s'>", $l[1]);
      }
    }}
    echo "</head><body>";
  }
  function hbottom () { echo "</body></html>"; }

  // (B) GENERATE USER QR LOGIN TOKEN
  function qr ($id, $for) {
    switch ($for) {
      // (B1) INVALID
      default: exit("Invalid request"); break;

      // (B2) ITEM QR CODE
      case "item":
        $this->Core->load("Items");
        $item = $this->Items->get($id);
        if (!is_array($item)) { exit("Invalid SKU"); }
        $qr = $item["item_sku"];
        break;
    
      // (B3) USER QR LOGIN
      case "user":
        $this->Core->load("QRIN");
        $qr = $this->QRIN->add($id);
        if ($qr===false) { exit("Invalid user"); }
        break;
    }

    // (B4) GENERATE QR HTML PAGE
    $this->htop([
      ["l", HOST_ASSETS."REPORT-qr.css"],
      ["s", HOST_ASSETS."qrcode.min.js"]
    ]);
    require PATH_PAGES . "REPORT-qr.php";
    $this->hbottom();
  }

  // (C) SUPPLIER ITEMS LIST
  function supitems ($id) {
    // (C1) GET SUPPLIER
    $sup = $this->DB->fetch("SELECT * FROM `suppliers` WHERE `sup_id`=?", [$id]);
    
    // (C2) FORCE DOWNLOAD AS CSV
    $name = str_replace(" ", "-", $sup["sup_name"]) . ".csv";
    header("Content-Disposition: attachment; filename=$name;");
    $f = fopen("php://output", "w");

    // (C3) HEADER - SUPPLIER
    fputcsv($f, [$sup["sup_name"]]);
    fputcsv($f, [$sup["sup_tel"], $sup["sup_email"], $sup["sup_address"]]);
    fputcsv($f, ["SKU", "Supplier SKU", "Name", "Description", "Unit", "Unit Price"]);

    // (C4) SUPPLIER ITEMS
    $this->DB->query(
      "SELECT * FROM `suppliers_items`
       LEFT JOIN `items` USING (`item_sku`)
       WHERE `sup_id`=?",
      [$id]
    );
    while ($r = $this->DB->stmt->fetch()) {
      fputcsv($f, [$r["item_sku"], $r["sup_sku"], $r["item_name"], $r["item_desc"], $r["item_unit"], $r["unit_price"]]);
    }
    fclose($f);
  }

  // (D) STOCK MOVEMENT REPORT
  function movement ($month, $year, $range) {
    // (D1) START & END OF MONTH & MOVE DIRECTION
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $month = $month<10 ? "0$month" : $month;
    $start = "$year-$month-01 00:00:00";
    $end = "$year-$month-$days 23:59:59";
    $this->Core->load("Settings");
    $this->Settings->defineN("STOCK_MVT", true);

    // (D2) OUTPUT CSV HEADERS
    header("Content-Disposition: attachment; filename=movement-$year-$month.csv;");
    $f = fopen("php://output", "w");

    // (D3) ALL MOVEMENT ENTRIES
    if ($range=="A") {
      fputcsv($f, ["Date", "Staff", "SKU", "Item", "Direction", "Quantity", "Unit", "Notes"]);
      $this->DB->query(
        "SELECT m.*, DATE_FORMAT(m.`mvt_date`, '".DT_LONG."') `md`, i.`item_name`, i.`item_unit` 
         FROM `item_mvt` m
         LEFT JOIN `items` i USING (`item_sku`)
         WHERE `mvt_date` BETWEEN ? AND ?
         ORDER BY m.`item_sku`, m.`mvt_date`",
        [$start, $end]
      );
      while ($r = $this->DB->stmt->fetch()) {
        fputcsv($f, [
          $r["md"], $r["user_name"],
          $r["item_sku"], $r["item_name"],
          STOCK_MVT[$r["mvt_direction"]], $r["mvt_qty"], $r["item_unit"],
          $r["mvt_notes"]
        ]);
      }
    }

    // (D4) SUMMARY
    else {
      // (D4-1) FETCH IN/OUT/DISPOSE ENTRIES
      $this->DB->query(
        "SELECT m.`item_sku` `s`, i.`item_name` `n`, i.`item_unit` `u`,
                m.`mvt_direction` `d`, SUM(m.`mvt_qty`) `q`
         FROM `item_mvt` m
         LEFT JOIN `items` i USING (`item_sku`)
         WHERE `mvt_date` BETWEEN ? AND ? AND `mvt_direction` IN ('I', 'O', 'D')
         GROUP BY m.`item_sku`, m.`mvt_direction`",
        [$start, $end]
      );
      $data = [];
      while ($r = $this->DB->stmt->fetch()) {
        if (!isset($data[$r["s"]])) {
          $data[$r["s"]] = [
            "n" => $r["n"],
            "u" => $r["u"]
          ];
        }
        $data[$r["s"]][$r["d"]] = $r["q"];
      }

      // (D4-2) OUTPUT CSV
      fputcsv($f, ["SKU", "Name", "Unit", "In", "Out", "Dispose"]);
      if (count($data)>0) { foreach($data as $sku=>$d) {
        fputcsv($f, [
          $sku, $d["n"], $d["u"], 
          isset($d["I"])?$d["I"]:0, isset($d["O"])?$d["O"]:0, isset($d["D"])?$d["D"]:0
        ]);
      }}
    }

    // (D5) THE END
    fclose($f);
  }

  // (E) ITEMS LIST
  function items ($range=null) {
    // (E1) HEADER
    header("Content-Disposition: attachment; filename=items-list.csv;");
    $f = fopen("php://output", "w");
    $now = $this->DB->fetchCol("SELECT DATE_FORMAT(CURRENT_TIMESTAMP(), '".DT_LONG."') `now`");
    fputcsv($f, ["ITEMS LIST AS AT " . strtoupper($now)]);
    fputcsv($f, ["SKU", "Name", "Description", "Quantity", "Unit"]);

    // (E2) ITEMS
    $sql = "SELECT * FROM `items`";
    if ($range=="M") { $sql .= " WHERE `item_low`>0"; }
    $this->DB->query($sql);
    while ($r = $this->DB->stmt->fetch()) {
      fputcsv($f, [
        $r["item_sku"], $r["item_name"], $r["item_desc"],
        $r["item_qty"], $r["item_unit"]
      ]);
    }
    fclose($f);
  }

  // (F) GET MONITORED ITEMS
  function getMonitor () {
    return $this->DB->fetchAll("SELECT * FROM `items` WHERE `item_low`>0");
  }

  // (G) DELIVERY ORDER
  function deliver ($id) {
    $this->Settings->defineN("DELIVER_STAT", true);
    $this->Core->load("Delivery");
    $order = $this->Delivery->get($id);
    $this->htop([
      ["l", HOST_ASSETS."REPORT-order.css"]
    ]);
    require PATH_PAGES . "REPORT-deliver.php";
    $this->hbottom();
  }

  // (H) PURCHASE ORDER
  function purchase ($id) {
    $this->Settings->defineN("PURCHASE_STAT", true);
    $this->Core->load("Purchase");
    $order = $this->Purchase->get($id);
    $this->htop([
      ["l", HOST_ASSETS."REPORT-order.css"]
    ]);
    require PATH_PAGES . "REPORT-purchase.php";
    $this->hbottom();
  }
}