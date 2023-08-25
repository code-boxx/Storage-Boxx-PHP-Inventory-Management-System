<?php
// (A) GET ITEM
$_CORE->load("Move");
$item = $_CORE->Move->getQ($_GET["sku"], $_GET["name"]);
if (!is_array($item)) { exit("Invalid SKU/batch"); }
$qr = json_encode(["S" => $_GET["sku"], "B" => $_GET["name"]]);

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
      <div id="qrsku"><?=$_GET["sku"]?> - <?=$_GET["name"]?></div>
      <div id="qritem"><?=$item["item_name"]?></div>
      <div id="qrexpire">EXPIRE : <?=$item["batch_expire"]?></div>
    </div>
  </body>
</html>