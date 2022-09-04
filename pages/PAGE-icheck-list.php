<?php
// (A) GET MOVEMENT HISTORY
$move = $_CORE->autoCall("Inventory", "getMove");
$_CORE->Settings->defineN("STOCK_MVT", true);

// (B) OUTPUT MOVEMENT HISTORY
if (is_array($move)) { foreach ($move as $m) { ?>
<div class="d-flex align-items-center border p-2">
  <div class="w-50 text-center">
    <div class="fw-bold"><?=STOCK_MVT[$m["mvt_direction"]]?></div>
    <div class="display-6"><?=$m["mvt_direction"]=="D"||$m["mvt_direction"]=="O"?"-":""?><?=$m["mvt_qty"]?></div>
  </div>
  <div class="w-50 text-secondary">
    <div><?=$m["mvt_date"]?></div>
    <div><?=$m["user_name"]?></div>
    <div><?=$m["mvt_notes"]?></div>
  </div>
</div>
<?php }} else { echo "No movement history"; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw("check.goToPage");