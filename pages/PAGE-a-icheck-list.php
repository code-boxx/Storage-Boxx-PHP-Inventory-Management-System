<?php
// (A) GET MOVEMENT HISTORY
$move = $_CORE->autoCall("Inventory", "getMove");
$mv = ["I"=>"In", "O"=>"Out", "T"=>"Take"];

// (B) OUTPUT MOVEMENT HISTORY
if (is_array($move["data"])) { foreach ($move["data"] as $m) { ?>
<div class="row">
  <div class="text-primary fw-bold">
    <?=$m["mvt_date"]?>
  </div>
  <div class="text-secondary">
    <?=$mv[$m["mvt_direction"]]?> |
    <?=$m["mvt_qty"]?> |
    <?=$m["user_name"]?>
  </div>
  <div class="text-secondary">
    <?=$m["mvt_notes"]?>
  </div>
</div>
<?php }} else { echo "No movement history"; }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw($move["page"], "check.goToPage");
