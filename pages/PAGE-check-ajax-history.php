<?php
// (A) GET ENTRIES
$_CORE->load("Page");
$move = $_CORE->Page->autoGet("Inventory", "countMove", "getMove");

// (B) OUTPUT MOVEMENT HISTORY 
$mv = ["I"=>"In", "O"=>"Out", "T"=>"Take"]; ?>
<table class="zebra">
  <?php if (is_array($move)) { foreach ($move as $m) { ?>
  <tr>
    <td>
      <?=$mv[$m['mvt_direction']]?> : <?=$m['mvt_qty']?>
    </td>
    <td><?=$m['mvt_date']?></td>
    <td><?=$m['user_name']?></td>
    <td><?=$m['mvt_notes']?></td>
  </tr>
  <?php }} else { echo "<tr><td>No movement history</td></tr>"; } ?>
</table>