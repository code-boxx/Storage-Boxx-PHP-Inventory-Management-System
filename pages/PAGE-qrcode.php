<?php
// (A) GENERATE QR CODE
switch ($_POST["for"]) {
  // (A1) INVALID
  default: exit("Invalid request"); break;

  // (A2) ITEM BATCH
  case "batch":
    $_CORE->load("Move");
    $item = $_CORE->Move->getQ($_POST["sku"], $_POST["name"]);
    if (!is_array($item)) { exit("Invalid SKU/batch"); }
    $qr = json_encode(["S" => $_POST["sku"], "B" => $_POST["name"]]);
    break;

  // (A3) USER QR LOGIN
  case "user":
    $_CORE->load("QRIN");
    $qr = $_CORE->QRIN->add($_POST["id"]);
    if ($qr===false) { exit("Invalid user"); }
    break;
}

// (B) QR CODE HTML ?>
<!DOCTYPE html>
<html>
  <head>
    <title>QR Code Generator</title>
    <style>
    * { font-family: Arial, sans-serif; box-sizing: border-box; }
    #qrwrap { width: 340px; padding: 20px; border: 1px solid #eee; }
    #qrsku, #qrexpire { font-weight: 700; }
    #qrsku { margin-top: 20px; font-size:1em; }
    #qritem { text-transform: uppercase; font-size: 1.7em; }
    #qrexpire { font-size: 0.8em; color: #e11a1a; }
    </style>
    <script src="<?=HOST_ASSETS?>qrcode.min.js"></script>
    <script>
    window.onload = () => new QRCode("qrcode", {
      text: '<?=$qr?>',
      width: 300, height: 300,
      colorDark : "#000000",
      colorLight : "#ffffff",
      correctLevel : QRCode.CorrectLevel.H
    });
    </script>
  </head>
  <body>
    <div id="qrwrap">
      <div id="qrcode"></div>
      <?php if ($_POST["for"]=="batch") { ?>
      <div id="qrsku"><?=$_POST["sku"]?> - <?=$_POST["name"]?></div>
      <div id="qritem"><?=$item["item_name"]?></div>
      <div id="qrexpire">EXPIRY : <?=$item["ex"]?strtoupper($item["ex"]):""?></div>
      <?php } ?>
    </div>
  </body>
</html>