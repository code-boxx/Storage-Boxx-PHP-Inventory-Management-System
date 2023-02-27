<?php
class Report extends Core {
  // (A) STOCK MOVEMENT REPORT
  function movement ($month, $year, $range) {
    // (A1) START & END OF MONTH & MOVE DIRECTION
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $month = $month<10 ? "0$month" : $month;
    $start = "$year-$month-01 00:00:00";
    $end = "$year-$month-$days 23:59:59";
    $this->Core->load("Settings");
    $this->Settings->defineN("STOCK_MVT", true);

    // (A2) OUTPUT CSV HEADERS
    header("Content-Disposition: attachment; filename=movement-$year-$month.csv;");
    $f = fopen("php://output", "w");

    // (A3) ALL MOVEMENT ENTRIES
    if ($range=="A") {
      fputcsv($f, ["Date", "Staff", "SKU", "Item", "Direction", "Quantity", "Left", "Notes"]);
      $this->DB->query(
        "SELECT m.*, DATE_FORMAT(m.`mvt_date`, '".DT_LONG."') `md`, s.`stock_name`, u.`user_name` 
         FROM `stock_mvt` m
         LEFT JOIN `stock` s USING (`stock_sku`)
         LEFT JOIN `users` u USING (`user_id`)
         WHERE `mvt_date` BETWEEN ? AND ?
         ORDER BY m.`stock_sku`, m.`mvt_date`",
        [$start, $end]
      );
      while ($r = $this->DB->stmt->fetch()) {
        fputcsv($f, [
          $r["md"], $r["user_name"],
          $r["stock_sku"], $r["stock_name"],
          STOCK_MVT[$r["mvt_direction"]], $r["mvt_qty"], $r["mvt_left"],
          $r["mvt_notes"]
        ]);
      }
    }

    // (A4) SUMMARY
    else {
      // (A4-1) FETCH IN/OUT/DISPOSE ENTRIES
      $this->DB->query(
        "SELECT m.`stock_sku` `s`, s.`stock_name` `n`, s.`stock_unit` `u`,
                m.`mvt_direction` `d`, SUM(m.`mvt_qty`) `q`
         FROM `stock_mvt` m
         LEFT JOIN `stock` s USING (`stock_sku`)
         WHERE `mvt_date` BETWEEN ? AND ? AND `mvt_direction` IN ('I', 'O', 'D')
         GROUP BY m.`stock_sku`, m.`mvt_direction`",
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

      // (A4-2) OUTPUT CSV
      fputcsv($f, ["SKU", "Name", "Unit", "In", "Out", "Dispose"]);
      if (count($data)>0) { foreach($data as $sku=>$d) {
        fputcsv($f, [
          $sku, $d["n"], $d["u"], 
          isset($d["I"])?$d["I"]:0, isset($d["O"])?$d["O"]:0, isset($d["D"])?$d["D"]:0
        ]);
      }}
    }

    // (A5) THE END
    fclose($f);
  }

  // (B) ITEMS LIST
  function items ($range=null) {
    // (B1) HEADER
    header("Content-Disposition: attachment; filename=items-list.csv;");
    $f = fopen("php://output", "w");
    $now = $this->DB->fetchCol("SELECT DATE_FORMAT(CURRENT_TIMESTAMP(), '".DT_LONG."') `now`");
    fputcsv($f, ["ITEMS LIST AS AT " . strtoupper($now)]);
    fputcsv($f, ["SKU", "Name", "Description", "Quantity", "Unit"]);

    // (B2) ITEMS
    $sql = "SELECT * FROM `stock`";
    if ($range=="M") { $sql .= " WHERE `stock_low`>0"; }
    $this->DB->query($sql);
    while ($r = $this->DB->stmt->fetch()) {
      fputcsv($f, [
        $r["stock_sku"], $r["stock_name"], $r["stock_desc"],
        $r["stock_qty"], $r["stock_unit"]
      ]);
    }
    fclose($f);
  }

  // (C) SUPPLIER ITEMS LIST
  function sitems ($id) {
    // (C1) GET SUPPLIER
    $sup = $this->DB->fetch("SELECT * FROM `suppliers` WHERE `sup_id`=?", [$id]);

    // (C2) CSV HEADER
    header("Content-Disposition: attachment; filename=items-list.csv;");
    $f = fopen("php://output", "w");
    fputcsv($f, [$sup["sup_name"]]);
    fputcsv($f, [$sup["sup_tel"], $sup["sup_email"], $sup["sup_address"]]);
    fputcsv($f, ["SKU", "Supplier SKU", "Name", "Description", "Unit", "Unit Price"]);

    // (C3) SUPPLIER ITEMS
    $this->DB->query(
      "SELECT * FROM `suppliers_items`
       LEFT JOIN `stock` USING (`stock_sku`)
       WHERE `sup_id`=?",
      [$id]
    );
    while ($r = $this->DB->stmt->fetch()) {
      fputcsv($f, [$r["stock_sku"], $r["sup_sku"], $r["stock_name"], $r["stock_desc"], $r["stock_unit"], $r["unit_price"]]);
    }
    fclose($f);
  }
}