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
1) Create a new database and import core/storage-boxx.sql.
2) Update core/Core.php - The database settings (A2), base URL (A4), email (A5)
3) Done! The default administrator account is admin@sb.com and password is 123456.


---
## TROUBLE?
---
1) Moved the domain? Simply delete .htaccess and let the system regenerate one.
2) Lost password? Write a simple script to update the user.
  - $_CORE->load("User");
  - $_CORE->User->save("NAME", "EMAIL", "PASSWORD", ID);


---
## DOCUMENTATION
---
Please visit https://code-boxx.com/storage-boxx/ for the documentation.