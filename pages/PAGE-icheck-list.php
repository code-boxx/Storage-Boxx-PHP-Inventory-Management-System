<?php
// (A) GET MOVEMENT HISTORY
$move = $_CORE->autoCall("Inventory", "getMove");
$stockOpt = ["I"=>"Stock In", "O"=>"Stock Out", "T"=>"Stock Take"];

?>

<?php if(is_array($move["data"])) {?>
  <table class="table">
    <thead>
      <tr>
        <th>Stock Movement Date</th>
        <th>User</th>
        <th>Notes</th>
        <th>Stock Options</th>
        <th>Stock Quantity</th>
      </tr>
    </thead>
    <tbody>
    <?php if (is_array($move["data"])) { foreach ($move["data"] as $m) { ?>
      <tr>
        <td><?=$m["mvt_date"]?></td>
        <td><?=$m["user_name"]?></td>
        <td><?=$m["mvt_notes"] ? $m["mvt_notes"] : "-"?></td>
        <td><?=$stockOpt[$m["mvt_direction"]]?></td>
        <td><?=$m["mvt_qty"]?></td>
      </tr>
      <?php }} ?>
    </tbody>
  </table>
<?php } else {?>
  <div class="d-flex align-items-center border p-2">No movement history.</div>
<?php } ?>
<?php

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw($move["page"], "check.goToPage");


