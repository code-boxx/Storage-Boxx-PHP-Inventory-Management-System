<h3 class="mb-3">WRITE NFC TAG</h3>

<div class="text-danger fw-bold">SKU: <?=$_POST["sku"]?></div>
<div class="bg-white border p-4 mb-3">
  <button class="btn btn-primary d-flex align-items-center" onclick="inv.nfcWrite()">
    <i class="mi">nfc</i> <span id="nfc-stat" class="ms-2"></span>
  </button>
</div>

<input type="button" class="col btn btn-danger" value="Back" onclick="inv.nfcBack()">