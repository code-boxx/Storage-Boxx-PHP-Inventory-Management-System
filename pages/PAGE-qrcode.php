<!DOCTYPE html>
<html>
  <head>
    <title>QR Code Generator</title>
    <style>
    * {
      font-family: Arial, sans-serif;
      box-sizing: border-box;
    }
    #qrwrap {
      width: 300px;
      padding: 20px;
      border: 1px solid #e1e1e1;
    }
    #qrsku {
      margin-top: 20px;
      color: #f00;
      font-size: 0.9em;
    }
    #qritem {
      text-transform: uppercase;
      font-size: 1.7em;
    }
    </style>
    <script src="<?=HOST_ASSETS?>qrcode.min.js"></script>
    <script>
    window.onload = () => {
      new QRCode("qrcode", {
        text: "<?=$_GET["sku"]?>",
        width: 256, height: 256,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
      });
    };
    </script>
  </head>
  <body>
    <div id="qrwrap">
      <div id="qrcode"></div>
      <div id="qrsku"><?=$_GET["sku"]?></div>
      <div id="qritem"><?=$_GET["name"]?></div>
    </div>
  </body>
</html>