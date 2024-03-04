<form id="iForm" onsubmit="return false">
  <?php if (I_APACHE === false || I_REWRITE === false) { ?>
  <!-- (C1) WARNINGS -->
  <div class="bg-danger text-white p-2 mb-2">
    If you are running Apache Web Server - Please enable <code class="text-white">MOD_REWRITE</code>.
  </div>
  <div class="bg-danger text-white p-2">
    If you are not running Apache Web Server, you can still try to proceed.
    After the installation, "translate" your own <code class="text-white">.htaccess</code> file.
  </div>
  <?php } ?>

  <!-- (C2) HEADER -->
  <div class="d-flex align-items-center mb-3">
    <img src="assets/favicon.png" class="me-2">
    <h1><?=strtoupper(SITE_NAME)?> INSTALLATION</h1>
  </div>

  <!-- (C3) HOST URL -->
  <h4 class="text-danger mb-3">HOST URL</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <select name="https" class="form-select">
        <option value="0">http://</option>
        <option value="1"<?=I_HTTPS?" selected":""?>>https://</option>
      </select>
      <label>HTTP or HTTPS</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="host" class="form-control" required value="<?=I_HOST?>">
      <label>Domain &amp; Path</label>
    </div>
    <div class="text-secondary">
      * Change this only if wrong, include the path if not deployed in root.
      E.G. <code>site.com/myproject/</code>
    </div>
  </div>

  <!-- (C4) API ENDPOINT -->
  <h4 class="text-danger mb-3">API ENDPOINT</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <select name="apihttps" class="form-select">
        <option value="0">No</option>
        <option value="1"<?=I_HTTPS?" selected":""?>>Yes</option>
      </select>
      <label>Enforce HTTPS?</label>
    </div>
    <div class="text-secondary">
      * Once enforced, API will only respond to HTTPS calls. Recommended for live servers.
    </div>
    <div class="text-secondary mb-2">
      * The host URL above need to be set to "HTTPS" if you want to enforce this.
    </div>

    <div class="form-floating mb-2">
      <select name="apicors" class="form-select" onchange="install.cors(this.value)">
        <option value="0">Disallow</option>
        <option value="1">Allow</option>
      </select>
      <label>CORS</label>
    </div>
    <div class="text-secondary">
      * Allow CORS only if you intend to develop mobile apps, or let third parties access your system.
    </div>

    <div id="corsmore" class="form-floating my-2 d-none">
      <input type="text" class="form-control" name="corsallow">
      <label>Allowed CORS Domains</label>
      <div class="text-secondary">
        * Leave this blank to allow all websites and apps to access your system (not recommended).
      </div>
      <div class="text-secondary">
        * To restrict which domains can access your system - Enter the domain name (<code>site-a.com</code>), or multiple domains separated by commas (<code>site-a.com, site-b.com</code>).
      </div>
    </div>
  </div>

  <!-- (C5) DATABASE -->
  <h4 class="text-danger mb-3">DATABASE</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <input type="text" name="dbhost" class="form-control" required value="<?=DB_HOST?>">
      <label>Host</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="dbname" class="form-control" required value="<?=DB_NAME?>">
      <label>Name</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="dbuser" class="form-control" required value="<?=DB_USER?>">
      <label>User</label>
    </div>

    <div class="form-floating">
      <input type="password" name="dbpass" class="form-control">
      <label>Password</label>
    </div>
  </div>

  <!-- (C6) EMAIL & TIMEZONE -->
  <h4 class="text-danger mb-3">SYSTEM DEFAULTS</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <input type="email" name="mailfrom" class="form-control" value="sys@site.com" required>
      <label>Email Sent From</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="timezone" class="form-control" value="<?=date_default_timezone_get()?>" required>
      <label>Timezone</label>
    </div>
    <div class="text-secondary">
      * If you wish to change it, see the full list of <a href="https://www.php.net/manual/en/timezones.php" target="_blank">supported timezones here</a>.
    </div>
  </div>

  <!-- (C7) COMPANY -->
  <?php if (I_CO) { ?>
  <h4 class="text-danger mb-3">COMPANY</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <input type="text" name="coname" class="form-control" required>
      <label>Company Name</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="coemail" class="form-control" required>
      <label>Company Email</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="cotel" class="form-control" required>
      <label>Company Tel</label>
    </div>

    <div class="form-floating mb-2">
      <textarea name="coaddr" class="form-control" required></textarea>
      <label>Company Address</label>
    </div>
  </div>
  <?php } ?>

  <!-- (C8) ADMIN USER -->
  <?php if (I_USER) { ?>
  <h4 class="text-danger mb-3">ADMIN USER</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <input type="text" name="aname" class="form-control" required value="Admin">
      <label>Name</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="aemail" class="form-control" required>
      <label>Email</label>
    </div>

    <div class="form-floating mb-2">
      <input type="password" name="apass" class="form-control" required>
      <label>Password</label>
    </div>
    <div class="text-secondary mb-2">* At least 8 characters alphanumeric.</div>

    <div class="form-floating mb-2">
      <input type="password" name="apassc" class="form-control" required>
      <label>Confirm Password</label>
    </div>
  </div>
  <?php } ?>

  <!-- (C9) JWT -->
  <h4 class="text-danger mb-3">JSON WEB TOKEN</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <input type="text" name="jwtkey" class="form-control" required>
      <label>Secret Key</label>
    </div>
    <div class="text-secondary mb-2" onclick="install.rnd()">
      * Click here to regenerate a random key.
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="jwyiss" class="form-control" required value="<?=$_SERVER["HTTP_HOST"]?>">
      <label>Issuer</label>
    </div>
    <div class="text-secondary">
      * Your company name or domain name.
    </div>
  </div>

  <!-- (C10) PUSH NOTIFICATION -->
  <?php if (I_PUSH) { ?>
  <h4 class="text-danger mb-3">WEB PUSH VAPID KEYS</h4>
  <div class="bg-light border p-3 mb-3">
    <div class="form-floating mb-2">
      <input type="text" name="pushprivate" class="form-control" required value="<?=I_VAPID["privateKey"]?>">
      <label>Private Key</label>
    </div>

    <div class="form-floating mb-2">
      <input type="text" name="pushpublic" class="form-control" required value="<?=I_VAPID["publicKey"]?>">
      <label>Public Key</label>
    </div>
    <div class="text-secondary">
      * You can regenerate these with:<br>
      <code>require "lib/webpush/autoload.php";</code><br>
      <code>$keys = Minishlink\WebPush\VAPID::createVapidKeys();</code>
    </div>
  </div>
  <?php } ?>

  <!-- (C11) GO! -->
  <input id="gobtn" type="submit" class="btn btn-primary" value="Go!" disabled>
</form>

<!-- (C12) DONE -->
<!-- @TODO - UPDATE LINKS FOR YOUR PROJECT -->
<div id="iCelebrate" class="d-none">
  <h1 class="my-4">INSTALLATION COMPLETE</h1>

  <h4 class="text-danger mb-3">QUICKSTART</h4>
  <div class="bg-light border p-3 mb-3">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/T-4FxpHE5xU?si=e6R2Cx_o6JFdOP3F" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
  </div>

  <h4 class="text-danger m-0">A FEW LINKS</h4>
  <div class="text-secondary mb-3"><small>
    * These links can be accessed in the "Help/About" section of the admin panel.
  </small></div>
  <div class="bg-light border p-3 mb-3"><ul class="list-group">
    <li class="list-group-item d-flex align-items-center">
      <a class="btn btn-danger" href="https://code-boxx.com/storage-boxx-php-inventory-system/" target="_blank">
        <i class="ico-sm icon-home3"></i> Official
      </a>
      <div class="ms-2">Official Storage Boxx Page (Documentation &amp; Stuff).</div>
    </li>
    <li class="list-group-item d-flex align-items-center">
      <a class="btn btn-danger" href="https://github.com/code-boxx/Storage-Boxx-PHP-Inventory-Management-System/issues/new/choose" target="_blank">
        <i class="ico-sm icon-bug"></i> Report
      </a>
      <div class="ms-2">Report a bug / Feature Request.</div>
    </li>
    <li class="list-group-item d-flex align-items-center">
      <a class="btn btn-danger" href="https://github.com/code-boxx/Storage-Boxx-PHP-Inventory-Management-System" target="_blank">
        <i class="ico-sm icon-star-full"></i> Star
      </a>
      <div class="ms-2">Just give a star to Storage Boxx on GitHub - It's free.</div>
    </li>
    <li class="list-group-item d-flex align-items-center">
      <a class="btn btn-danger" href="https://github.com/sponsors/code-boxx?frequency=one-time" target="_blank">
        <i class="ico-sm icon-heart"></i> Donate
      </a>
      <div class="ms-2">Buy a malnourished developer some food. Even a small one-time amount helps.</div>
    </li>
    <li class="list-group-item d-flex align-items-center">
      <a class="btn btn-danger" id="iDone">
        <i class="ico-sm icon-checkmark"></i> Done
      </a>
      <div class="ms-2">To the home page.</div>
    </li>
  </ul></div>
</div>