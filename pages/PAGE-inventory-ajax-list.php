<?php
// (A) GET ITEMS
$_CORE->load("Page");
$items = $_CORE->Page->autoGet("Inventory", "countAll", "getAll");

// (B) DRAW ITEMS LIST
if (is_array($items)) { ?> 
<table class="zebra">
  <?php foreach ($items as $sku=>$i) {?>
  <tr>
    <td>
      <strong>[<?=$sku?>] <?=$i['stock_name']?></strong><br>
      <small><?=$i['stock_desc']?></small><br>
      <small><?=$i['stock_qty']?> <?=$i['stock_unit']?></small>
    </td>
    <td class="right">
      <input type="button" value="Delete" onclick="inv.del('<?=$sku?>')"/>
      <input type="button" value="Edit" onclick="inv.addEdit('<?=$sku?>')"/>
      <input type="button" value="Barcode" onclick="inv.barcode('<?=$sku?>')"/>
    </td>
  </tr>
  <?php } ?>
</table>
<?php } else { echo "<div>No items found.</div>"; }

// (C) PAGINATION
$_CORE->Page->draw("inv.goToPage", "J");