<div class="title mb">PURCHASE ORDER</div>

<!-- (A) HEADER -->
<div class="flex mb">
  <!-- (A2) FROM & TO -->
  <div class="grow box">
    <div class="subtitle">VENDOR</div>
    <div><?=$order["sup_name"]?></div>
    <div><?=nl2br($order["sup_address"])?></div>
    <div>
    T: <?=$order["sup_tel"]?>
      E: <?=$order["sup_email"]?>
    </div>
    <br>

    <div class="subtitle">SHIP TO</div>
    <div><?=$order["p_name"]?></div>
    <div><?=nl2br($order["p_address"])?></div>
    <div>
      T: <?=$order["p_tel"]?>
      E: <?=$order["p_email"]?>
    </div>
  </div>

  <!-- (A3) ORDER ID, DATE, STATUS -->
  <div>
    <div class="box">
      <div class="subtitle">ORDER #</div>
      <?=$order["p_id"]?>
    </div>
    <div class="box">
      <div class="subtitle">PURCHASE DATE</div>
      <?=$order["p_date"]?>
    </div>
    <div class="box">
      <div class="subtitle">STATUS</div>
      <?=PURCHASE_STAT[$order["p_status"]]?>
    </div>
  </div>
</div>

<!-- (B) ORDER ITEMS -->
<table class="list mb">
  <thead><tr>
    <th>Qty</th>
    <th>Item</th>
    <th>Unit Price</th>
    <th>Amount</th>
  </tr></thead>
  <tbody><?php foreach ($order["items"] as $i) { ?>
  <tr>
    <td><?=$i[5]?> <?=$i[3]?></td>
    <td>[<?=$i[1]?>] <?=$i[2]?></td>
    <td><?=$i[4]?></td>
    <td><?=$i[4] * $i[5]?></td>
  </tr>
  <?php } ?></tbody>
</table>

<!-- (C) NOTES -->
<div class="box">
  <div class="subtitle">NOTES</div>
  <?=nl2br($order["p_notes"])?>
</div>

<script>
window.addEventListener("load", window.print);
</script>