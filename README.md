## STORAGE BOXX
Storage Boxx is a simple PHP Inventory Management System. This is not the “super professional crazy bloated with a lot of features” kind of system. But it does cover the basics and has a built-in HTML webcam QR code scanner. This should help the small businesses get started with their inventory stock keeping.
<br><br>


## SCREENSHOTS
<p float="left">
  <img width="250" style="inline-block" src="https://github.com/code-boxx/Storage-Boxx/blob/main/assets/ss-sb-0.jpg">
  <img width="250" style="inline-block" src="https://github.com/code-boxx/Storage-Boxx/blob/main/assets/ss-sb-1.jpg">
  <img width="250" style="inline-block" src="https://github.com/code-boxx/Storage-Boxx/blob/main/assets/ss-sb-2.jpg">
  <img width="250" style="inline-block" src="https://github.com/code-boxx/Storage-Boxx/blob/main/assets/ss-sb-3.jpg">
</p><br>

## INSTALLATION & DOCUMENTATION
Just access `index.php` in your browser and walk through the installer.

Visit https://code-boxx.com/storage-boxx-php-inventory-system/ for the documentation.
<br><br>


## UPDATING
* If your existing copy has an `options` table - Just override all the existing files and access `index.php`. The installer will take care of database updates (if any).
* If not – Manually import the `options` table from `SQL-storage-boxx.sql`, create a `STOREBOXX_VER` entry with value of `0` and group `0`. Thereafter, just copy the new files and let the installer do the magic.
<br><br>


## REQUIREMENTS
1) Not extensively tested, but should work with at least PHP 8.0.
2) PHP MYSQL PDO extension.
3) Apache server with MOD REWRITE enabled.
4) "Grade A" browser.
<br><br>


## LICENSE
Copyright by Code Boxx

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
<br><br>


## CREDITS
1) HTML5 QRCode Scanner https://github.com/mebjashtml5-qrcode
2) QRCode Generator https://davidshimjs.github.io/qrcodejs/
