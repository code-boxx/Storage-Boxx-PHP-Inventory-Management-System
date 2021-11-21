<?php
class BooBoo extends Core {
  function ouch ($ex) {
    // API MODE - OUTPUT JSON ENCODED MESSAGE
    if (defined("API_MODE")) {
      $this->core->respond(0,
      ERR_SHOW ? $ex->getMessage() : "OPPS! An error has occured.",
      ERR_SHOW ? [
        "code" => $ex->getCode(),
        "file" => $ex->getFile(),
        "line" => $ex->getLine()
        ] : null
      );
    }

    // WEB MODE - SHOW HTML ERROR MESSAGE
    else { ?>
    <div style="box-sizing:border-box;position:fixed;width:100vw;height:100vh;z-index:9999;background:#fff;color:#000;padding:30px;font-family:arial">
      <h1 style="font-size:50px;padding:0;margin:0">(╯°□°)╯︵ ┻━┻</h1>
      <p style="font-size:30px;color:#ff4545">AN ERROR HAS OCCURED.</p>
      <?php if (ERR_SHOW) { ?>
      <table>
        <tr>
          <td style="font-weight:700">Message</td>
          <td><?=$ex->getMessage()?></td>
        </tr>
        <tr>
          <td style="font-weight:700">Code</td>
          <td><?=$ex->getCode()?></td>
        </tr>
        <tr>
          <td style="font-weight:700">File</td>
          <td><?=$ex->getFile()?></td>
        </tr>
        <tr>
          <td style="font-weight:700">Line</td>
          <td><?=$ex->getLine()?></td>
        </tr>
        <tr>
          <td style="font-weight:700">Trace</td>
          <td><?=$ex->getTraceAsString()?></td>
        </tr>
      </table>
      <?php } ?>
    </div>
    <?php }
  }
}
