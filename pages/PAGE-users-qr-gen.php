<?php
// (A) CREATE USER QR LOGIN
$_CORE->load("QRIN");
$qr = $_CORE->QRIN->add($_POST["id"]);

// (B) QR CODE HTML ?>
<!DOCTYPE html>
<html>
  <head>
    <title>QR Code Generator</title>
    <style>
    * { font-family: Arial, sans-serif; box-sizing: border-box; }
    #qrwrap { width: 340px; padding: 20px; border: 1px solid #eee; }
    </style>
    <script src="<?=HOST_ASSETS?>qrcode.min.js"></script>
    <script>
    window.onload = () => new QRCode("qrcode", {
      text: "<?=$qr?>",
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
    </div>
  </body>
</html>