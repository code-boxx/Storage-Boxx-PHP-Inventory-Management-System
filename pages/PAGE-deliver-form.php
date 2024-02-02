<?php
// (A) GET DELIVERY
$_CORE->Settings->defineN("DELIVER_STAT", true);
$edit = isset($_POST["id"]) && $_POST["id"]!="";
if ($edit) { $dlv = $_CORE->autoCall("Delivery", "get"); }

// (B) DELIVERY FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> DELIVERY</h3>
<form onsubmit="return dlv.save()">
  <!-- (B1) CUSTOMER -->
  <div class="fw-bold text-danger mb-2">CUSTOMER</div>
  <div class="bg-white border p-4 mb-3">
    <div class="form-floating mb-4">
      <input type="text" id="d-name" class="form-control" required value="<?=$edit?$dlv["d_name"]:""?>">
      <label>Name</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" id="d-tel" class="form-control" required value="<?=$edit?$dlv["d_tel"]:""?>">
      <label>Telephone</label>
    </div>

    <div class="form-floating mb-0">
      <input type="email" id="d-email" class="form-control" required value="<?=$edit?$dlv["d_email"]:""?>">
      <label>Email</label>
    </div>
  </div>

  <!-- (B2) DELIVERY -->
  <div class="fw-bold text-danger mb-2">DELIVERY</div>
  <div class="bg-white border p-4 mb-3">
    <input type="hidden" id="d-id" value="<?=$edit?$dlv["d_id"]:""?>">

    <?php if ($edit) { ?>
    <div class="form-floating mb-1">
      <select id="d-stat" class="form-select"><?php
        foreach (DELIVER_STAT as $i=>$n) {
          printf("<option %svalue='%s'>%s</option>",
            $i==$dlv["d_status"] ? " selected " : "",
            $i, $n
          );
        }
      ?></select>
      <label>Status</label>
    </div>
    <div class="text-secondary mb-4">
      * Order cannot be edited once completed or cancelled.<br>
      * Stock will be automatically deducted on complete.
    </div>
    <?php } ?>

    <div class="form-floating mb-4">
      <input type="text" id="d-address" class="form-control" value="<?=$edit?$dlv["d_address"]:""?>">
      <label>Address</label>
    </div>
   <div class="form-floating mb-4">
      <input type="date" id="d-date" class="form-control" required value="<?=$edit?$dlv["d_date"]:""?>">
      <label>Date</label>
    </div>
    <div class="form-floating">
      <textarea id="d-notes" class="form-control"><?=$edit?$dlv["d_notes"]:""?></textarea>
      <label>Notes (If Any)</label>
    </div>
  </div>

  <!-- (B3) ITEMS LIST -->
  <div class="fw-bold text-danger mb-2">ITEMS</div>
  <div id="dlv-items" class="bg-white border p-4 mb-3 zebra">
    <?php
    if ($edit) {
      printf("<div id='dlv-items-data' class='d-none'>%s</div>", json_encode($dlv["items"]));
    }
    ?>
  </div>

  <div class="fw-bold text-danger mb-2">ADD ITEM</div>
  <div class="bg-white border p-4 mb-3">
    <div class="form-floating mb-2">
      <input type="text" class="form-control" id="add-item">
      <label>Item SKU</label>
    </div>
    <button type="button" class="btn btn-primary d-flex-inline" onclick="dlv.addQR()">
      <i class="ico-sm icon-qrcode"></i> Scan
    </button>
    <button id="nfc-btn" type="button" disabled class="btn btn-primary d-flex-inline" onclick="nfc.scan()">
      <i class="ico-sm icon-feed"></i> Scan
    </button>
  </div>

  <button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="cb.page(1)">
    <i class="ico-sm icon-undo2"></i> Back
  </button>
  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-checkmark"></i> Save
  </button>
</form>