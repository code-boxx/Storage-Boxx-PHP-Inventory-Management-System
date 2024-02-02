<h3 class="mb-3">WRITE NFC TAG</h3>

<div class="bg-white border p-4 mb-3">
  <div class="form-floating mb-4">
    <input type="text" readonly class="form-control" value="<?=$_POST["sku"]?>">
    <label>SKU</label>
  </div>
</div>

<button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="item.nfcBack()">
  <i class="ico-sm icon-undo2"></i> Back
</button>
<button type="button" id="nfc-btn" disabled class="my-1 btn btn-primary d-flex-inline" onclick="item.nfcWrite()">
  <i class="ico-sm icon-feed"></i> <span id="nfc-stat" class="ms-2">Initializing</span>
</button>