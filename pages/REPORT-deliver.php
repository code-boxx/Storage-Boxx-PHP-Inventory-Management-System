<!-- (A) HEADER -->
<div id="d-title">DELIVERY ORDER</div>

<!-- (A1) LEFT : DELIVER TO -->
<div id="d-head">
  <div id="d-head-left" class="box">
    <div class="subtitle">DELIVER TO</div>
    <div><?=$order["d_name"]?></div>
    <div><?=$order["d_address"]?></div>
    <div>
      <?=$order["d_tel"]?>
      <?=$order["d_email"]?>
    </div>
  </div>

  <!-- (A2) RIGHT : ORDER ID, DATE, STATUS -->
  <div id="d-head-right">
    <div class="box">
      <div class="subtitle">ORDER #</div>
      <?=$order["d_id"]?>
    </div>
    <div class="box">
      <div class="subtitle">DELIVERY DATE</div>
      <?=$order["d_date"]?>
    </div>
    <div class="box">
      <div class="subtitle">STATUS</div>
      <?=DELIVER_STAT[$order["d_status"]]?>
    </div>
  </div>
</div>

<!-- (B) ORDER ITEMS -->
<table id="d-items">
  <thead><tr>
    <th>Qty</th>
    <th>Item</th>
    <th>Unit Price</th>
    <th>Amount</th>
  </tr></thead>
  <tbody><?php foreach ($items as $i) { ?>
  <tr>
    <td><?=$i["item_qty"]?> <?=$i["item_unit"]?></td>
    <td>[<?=$i["item_sku"]?>] <?=$i["item_name"]?></td>
    <td><?=$i["item_price"]?></td>
    <td><?=$i["item_price"]?></td>
  </tr>
  <?php } ?></tbody>
</table>

<!-- (C) NOTES -->
<div id="d-notes" class="box">
  <div class="subtitle">NOTES</div>
  <?=nl2br($order["d_notes"])?>
</div>