<!-- (A) SCRIPTS -->
<script src="<?=HOST_ASSETS?>move.js"></script>
<script src="<?=HOST_ASSETS?>checker.js"></script>

<!-- (B) STOCK MOVEMENT -->
<!-- (B1) HEADER -->
<nav class="navbar text-white sb-grey mb-4">
<div class="container-fluid">
  <h4>Stock Movement</h4>
  <form class="d-flex" onsubmit="return check.verify()">
    <input type="text" id="mvt-check" placeholder="Check SKU" class="form-control form-control-sm"/>
    <button class="btn btn-primary">
      <span class="mi">history</span>
    </button>
  </form>
</div>
</nav>

<div class="row">
  <!-- (B2) MOVEMENT FORM -->
  <form class="col bg-light border p-4 m-1" autocomplete="off" onsubmit="return move.save()">
    <div class="mb-4">
      <label class="form-label" for="mvt-direction">Direction</label>
      <select class="form-control" id="mvt-direction">
        <option value="I">Stock In (Receive)</option>
        <option value="O">Stock Out (Release)</option>
        <option value="T">Stock Take (Audit)</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="form-label" for="mvt-qty">Quantity</label>
      <input type="number" class="form-control" id="mvt-qty" step="0.01" value="1.00" required/>
    </div>

    <div class="mb-4">
      <label class="form-label" for="mvt-notes">Notes (If Any)</label>
      <textarea class="form-control" id="mvt-notes"></textarea>
    </div>

    <div class="mb-4">
      <label class="form-label" for="mvt-sku">SKU (Enter or Scan)</label>
      <input type="text" class="form-control" id="mvt-sku" required autofocus/>
    </div>

    <input type="submit" class="btn btn-primary" value="Save"/>
  </form>

  <!-- (B3) MOVEMENT HISTORY (FOR CURRENT SESSION) -->
  <div class="col bg-light border p-4 m-1">
    <ul class="list-group list-group-flush" id="mvt-result"></ul>
  </div>
</div>
