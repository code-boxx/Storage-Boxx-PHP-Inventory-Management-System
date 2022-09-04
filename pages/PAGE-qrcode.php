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
    #qrtxt {
      margin-top: 20px;
      font-weight: 700;
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
      <div id="qrtxt">
        <?=$_GET["sku"]?><br><?=$_GET["name"]?>
      </div>
    </div>
  </body>
</html>