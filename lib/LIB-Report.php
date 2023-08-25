<?php
class Report extends Core {
  // (A) SUPPLIER ITEMS LIST
  function supitems ($id) {
    // (A1) GET SUPPLIER
    $sup = $this->DB->fetch("SELECT * FROM `suppliers` WHERE `sup_id`=?", [$id]);
    
    // (A2) FORCE DOWNLOAD AS CSV
    $name = str_replace(" ", "-", $sup["sup_name"]) . ".csv";
    header("Content-Disposition: attachment; filename=$name;");
    $f = fopen("php://output", "w");

    // (A3) HEADER - SUPPLIER
    fputcsv($f, [$sup["sup_name"]]);
    fputcsv($f, [$sup["sup_tel"], $sup["sup_email"], $sup["sup_address"]]);
    fputcsv($f, ["SKU", "Supplier SKU", "Name", "Description", "Unit", "Unit Price"]);

    // (A4) SUPPLIER ITEMS
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

  // (B) STOCK MOVEMENT REPORT
  function movement ($month, $year, $range) {
    // (B1) START & END OF MONTH & MOVE DIRECTION
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $month = $month<10 ? "0$month" : $month;
    $start = "$year-$month-01 00:00:00";
    $end = "$year-$month-$days 23:59:59";
    $this->Core->load("Settings");
    $this->Settings->defineN("STOCK_MVT", true);

    // (B2) OUTPUT CSV HEADERS
    header("Content-Disposition: attachment; filename=movement-$year-$month.csv;");
    $f = fopen("php://output", "w");

    // (B3) ALL MOVEMENT ENTRIES
    if ($range=="A") {
      fputcsv($f, ["Date", "Staff", "SKU", "Batch", "Item", "Direction", "Quantity", "Unit", "Notes"]);
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
          $r["item_sku"], $r["batch_name"], $r["item_name"],
          STOCK_MVT[$r["mvt_direction"]], $r["mvt_qty"], $r["item_unit"],
          $r["mvt_notes"]
        ]);
      }
    }

    // (B4) SUMMARY
    else {
      // (B4-1) FETCH IN/OUT/DISPOSE ENTRIES
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

      // (B4-2) OUTPUT CSV
      fputcsv($f, ["SKU", "Name", "Unit", "In", "Out", "Dispose"]);
      if (count($data)>0) { foreach($data as $sku=>$d) {
        fputcsv($f, [
          $sku, $d["n"], $d["u"], 
          isset($d["I"])?$d["I"]:0, isset($d["O"])?$d["O"]:0, isset($d["D"])?$d["D"]:0
        ]);
      }}
    }

    // (B5) THE END
    fclose($f);
  }

  // (C) ITEMS LIST
  function items ($range=null) {
    // (C1) HEADER
    header("Content-Disposition: attachment; filename=items-list.csv;");
    $f = fopen("php://output", "w");
    $now = $this->DB->fetchCol("SELECT DATE_FORMAT(CURRENT_TIMESTAMP(), '".DT_LONG."') `now`");
    fputcsv($f, ["ITEMS LIST AS AT " . strtoupper($now)]);
    fputcsv($f, ["SKU", "Name", "Description", "Quantity", "Unit"]);

    // (C2) ITEMS
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
}