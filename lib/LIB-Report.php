<?php
class Report extends Core {
  // (A) STOCK MOVEMENT REPORT
  function movement ($month, $year) {
    // (A1) START & END OF MONTH & MOVE DIRECTION
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $month = $month<10 ? "0$month" : $month;
    $start = "$year-$month-01 00:00:00";
    $end = "$year-$month-$days 23:59:59";
    $this->core->load("Settings");
    $this->core->Settings->defineN("STOCK_MVT", true);

    // (A2) OUTPUT CSV
    header("Content-Disposition: attachment; filename=movement-$year-$month.csv;");
    $f = fopen("php://output", "w");
    fputcsv($f, ["Date", "Staff", "SKU", "Item", "Direction", "Quantity", "Notes"]);
    $this->DB->query(
      "SELECT m.*, s.`stock_name`, u.`user_name` 
       FROM `stock_mvt` m
       LEFT JOIN `stock` s USING (`stock_sku`)
       LEFT JOIN `users` u USING (`user_id`)
       WHERE `mvt_date` BETWEEN ? AND ?",
      [$start, $end]
    );
    while ($r = $this->DB->stmt->fetch()) {
      fputcsv($f, [
        $r["mvt_date"], $r["user_name"],
        $r["stock_sku"], $r["stock_name"],
        STOCK_MVT[$r["mvt_direction"]], $r["mvt_qty"],
        $r["mvt_notes"]
      ]);
    }
    fclose($f);
  }

  // (B) ITEMS LIST
  function items ($range=null) {
    // (B1) HEADER
    header("Content-Disposition: attachment; filename=items-list.csv;");
    $f = fopen("php://output", "w");
    fputcsv($f, ["ITEMS LIST AS AT ".date("Y-m-d H:i:s")]);
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
}