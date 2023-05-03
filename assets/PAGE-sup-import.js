var simport = {
  // (A) LOAD "IMPORT SUPPLIERS" PAGE
  init : () => {
    cb.page(2);
    cb.load({
      page : "suppliers/import",
      target : "cb-page-2"
    });
    return false;
  },

  // (B) READ CSV FILE
  read : () => {
    // (B1) HTML ELEMENTS
    let hSelect = document.getElementById("sup-import-select"),
        hFile = document.getElementById("sup-import-file"),
        hTable = document.getElementById("sup-import-table"),
        hList = document.getElementById("sup-import-list");
    hSelect.classList.add("d-none");
    hTable.classList.remove("d-none");

    // (B2) READ SELECTED FILE
    let reader = new FileReader(),
        vMail = new RegExp("[a-z0-9]+@[a-z]+\.[a-z]{2,3}"),
        csv = hFile.files[0], row, col, err, valid = false;

    reader.addEventListener("loadend", () => { try {
      // (B2-1) READ ROW-BY-ROW INTO HTML + CHECK VALID
      CSV.parse(reader.result).forEach(r => {
        row = document.createElement("tr");
        if (r.length != 4) {
          row.className = "table-danger fw-bold";
          row.innerHTML = `<td colspan="4">?</td><td>Invalid Row</td>`;
        } else {
          err = null;
          for (let i=0; i<4; i++) {
            col = document.createElement("td");
            col.innerHTML = r[i]==null?"":r[i];
            row.appendChild(col);
          }
          if (r[0]===null || r[0]==="") { err = "Missing Name"; }
          if (err==null && (r[1]===null || r[1]==="")) { err = "Missing Tel"; }
          if (err==null && !vMail.test(r[2])) { err = "Invalid Email"; }
          col = document.createElement("td");
          col.innerHTML = err==null ? "" : err;
          row.appendChild(col);
          if (err==null) {
            row.className = "valid";
            valid = true;
          } else { row.className = "table-danger fw-bold"; }
        }
        hList.appendChild(row);
      });

      // (B2-2) START BUTTON
      if (valid) {
        row = document.createElement("tr");
        row.innerHTML = `<td colspan="5">
          <button id="sup-import-go" class="btn btn-primary" onclick="simport.go(1)">Start Import</button>
        </td>`;
        hList.appendChild(row);
      }
    } catch (err) {
      cb.modal("Error opening CSV file", err.message)
      console.error(err);
    }});
    reader.readAsText(csv);
  },

  // (C) START IMPORT
  go : first => {
    // (C1) BLOCK SCREEN & DISABLE BUTTON ON INIT CALL
    if (first) {
      document.getElementById("sup-import-go").disabled = true;
      cb.loading(true);
    }

    // (C2) IMPORT ENTRY
    let row = document.querySelector("#sup-import-list .valid");
    if (row!=null) {
      let col = row.querySelectorAll("td");
      row.classList.remove("valid");
      cb.api({
        mod : "suppliers", act : "import",
        passmsg : false, loading : false, nofail : true,
        data : {
          name : col[0].innerHTML,
          tel : col[1].innerHTML,
          email : col[2].innerHTML,
          addr : col[3].innerHTML
        },
        onpass : () => {
          row.classList.remove("valid");
          col[4].innerHTML = "OK";
          simport.go();
        },
        onfail : msg => {
          row.classList.remove("valid");
          row.classList.add("table-danger");
          col[4].innerHTML = msg;
          simport.go();
        }
      });
    }

    // (C3) ALL DONE
    else {
      let btn = document.getElementById("sup-import-go");
      btn.innerHTML = "Done - Go Back";
      btn.onclick = () => cb.page(1);
      btn.disabled = false;
      sup.list(true);
      cb.loading(false);
    }
  }
};