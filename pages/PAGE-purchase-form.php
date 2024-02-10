<?php
// (A) GET PURCHASE
$_CORE->Settings->defineN("PURCHASE_STAT", true);
$edit = isset($_POST["id"]) && $_POST["id"]!="";
if ($edit) { $pur = $_CORE->autoCall("Purchase", "get"); }

// (B) PURCHASE FORM ?>
<h3 class="mb-3"><?=$edit?"EDIT":"ADD"?> PURCHASE</h3>
<form onsubmit="return pur.save()">
  <!-- (B1) SUPPLIER -->
  <div class="fw-bold text-danger mb-2">SUPPLIER &amp; ORDER</div>
  <div class="bg-white border p-4 mb-3">
    <input type="hidden" id="p-id" value="<?=$edit?$pur["p_id"]:""?>">

    <div class="form-floating">
      <input type="text" id="sup-name" class="form-control" <?=$edit?" disabled ":""?>value="<?=$edit?$pur["sup_name"]:""?>">
      <label>Supplier Name</label>
    </div>
    <div id="sup-change" class="d-none text-secondary" onclick="pur.csup(true)">* Click here to change supplier, all current items will be removed.</div>
    <input type="hidden" id="sup-id" value="<?=$edit?$pur["sup_id"]:""?>">

    <?php if ($edit) { ?>
    <div class="form-floating mt-4 mb-1">
      <select id="p-stat" class="form-select"><?php
        foreach (PURCHASE_STAT as $i=>$n) {
          printf("<option %svalue='%s'>%s</option>",
            $i==$pur["p_status"] ? " selected " : "",
            $i, $n
          );
        }
      ?></select>
      <label>Status</label>
    </div>
    <div class="text-secondary">
      * Order cannot be edited once completed or cancelled.<br>
      * Stock will be automatically deducted on complete.
    </div>
    <?php } ?>
  </div>

  <!-- (B2) SHIP TO -->
  <div class="fw-bold text-danger mb-2">SHIP TO</div>
  <div class="bg-white border p-4 mb-3">
    <div class="form-floating mb-4">
      <input type="text" id="p-name" class="form-control" required value="<?=$edit?$pur["p_name"]:CO_NAME?>">
      <label>Name</label>
    </div>

    <div class="form-floating mb-4">
      <input type="text" id="p-tel" class="form-control" required value="<?=$edit?$pur["p_tel"]:CO_TEL?>">
      <label>Telephone</label>
    </div>

    <div class="form-floating mb-4">
      <input type="email" id="p-email" class="form-control" required value="<?=$edit?$pur["p_email"]:CO_EMAIL?>">
      <label>Email</label>
    </div>

    <div class="form-floating mb-4">
    <textarea id="p-address" class="form-control" required><?=$edit?$pur["p_address"]:CO_ADDRESS?></textarea>
      <label>Address</label>
    </div>

    <div class="form-floating mb-4">
      <input type="date" id="p-date" class="form-control" required value="<?=$edit?$pur["p_date"]:""?>">
      <label>Date</label>
    </div>

    <div class="form-floating">
      <textarea id="p-notes" class="form-control"><?=$edit?$pur["p_notes"]:""?></textarea>
      <label>Notes (If Any)</label>
    </div>
  </div>

  <!-- (B3) ITEMS LIST -->
  <div class="fw-bold text-danger">ITEMS</div>
  <div class="text-secondary mb-2">* Drag-and-drop to sort.</div>
  <div id="pur-items" class="bg-white border p-4 mb-3 zebra">
    <?php
    if ($edit) {
      printf("<div id='pur-items-data' class='d-none'>%s</div>", json_encode($pur["items"]));
    }
    ?>
  </div>

  <div class="fw-bold text-danger mb-2">ADD ITEM</div>
  <div class="bg-white border p-4 mb-3">
    <div class="form-floating mb-2">
      <input type="text" class="form-control" id="add-item"<?=$edit?"":" disabled"?>>
      <label>Item SKU/Name</label>
    </div>
    <button id="qr-btn" type="button" class="btn btn-primary d-flex-inline" onclick="pur.addQR()"<?=$edit?"":" disabled"?>>
      <i class="ico-sm icon-qrcode"></i> Scan
    </button>
    <button id="nfc-btn" type="button" class="btn btn-primary d-flex-inline" onclick="nfc.scan()" disabled>
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