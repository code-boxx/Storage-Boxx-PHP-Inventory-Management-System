---
## LICENSE
---

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


---
## INSTALLATION
---
1) Create a new database and import lib/storage-boxx.sql.
2) Access the site, follow through the installer.


---
## TROUBLE?
---
1) Changed the path? Change .htaccess in both the root and api folder.
2) Lost password? Write a simple script to update the user.
  - $_CORE->load("User");
  - $_CORE->User->save("NAME", "EMAIL", "PASSWORD", ID);


---
## DOCUMENTATION
---
Please visit https://code-boxx.com/storage-boxx/ for the documentation.


---
## CREDITS
---
HTML5 QRCode Scanner https://github.com/mebjashtml5-qrcode
QRCode Generator https://davidshimjs.github.io/qrcodejs/
