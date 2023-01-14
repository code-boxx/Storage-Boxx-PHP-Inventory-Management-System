var iimport = {
  // (A) LOAD "IMPORT SUPPLIER ITEMS" PAGE
  init : () => {
    cb.page(2);
    cb.load({
      page : "suppliers/items/import",
      target : "cb-page-3"
    });
    return false;
  },

  // (B) READ CSV FILE
  read : () => {
    // (B1) HTML ELEMENTS
    let hSelect = document.getElementById("sup-items-import-select"),
        hFile = document.getElementById("sup-items-file"),
        hTable = document.getElementById("sup-items-import-table"),
        hList = document.getElementById("sup-items-import-list");
    hSelect.classList.add("d-none");
    hTable.classList.remove("d-none");

    // (B2) READ SELECTED FILE
    let reader = new FileReader(),
        csv = hFile.files[0], row, col, err, valid = false;

    reader.addEventListener("loadend", () => { try {
      // (B2-1) READ ROW-BY-ROW INTO HTML + CHECK VALID
      CSV.parse(reader.result).forEach(r => {
        row = document.createElement("tr");
        if (r.length != 3) {
          row.className = "table-danger fw-bold";
          row.innerHTML = `<td colspan="3">?</td><td>Invalid Row</td>`;
        } else {
          err = null;
          for (let i=0; i<3; i++) {
            col = document.createElement("td");
            col.innerHTML = r[i]==null?"":r[i];
            row.appendChild(col);
          }
          if (r[0]===null || r[0]==="") { err = "Missing SKU"; }
          if (err==null && (isNaN(r[2]) || r[2]===null || r[2]==="")) { err = "Invalid Unit Price"; }
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
        row.innerHTML = `<td colspan="4">
          <button id="sup-items-import-go" class="btn btn-primary" onclick="iimport.go(1)">Start Import</button>
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
      document.getElementById("sup-items-import-go").disabled = true;
      cb.loading(true);
    }

    // (C2) IMPORT ENTRY
    let row = document.querySelector("#sup-items-import-list .valid");
    if (row!=null) {
      let col = row.querySelectorAll("td");
      row.classList.remove("valid");
      cb.api({
        mod : "suppliers", req : "importItem",
        passmsg : false, loading : false, nofail : true,
        data : {
          id : items.id,
          sku : col[0].innerHTML,
          ssku : col[1].innerHTML,
          price : col[2].innerHTML
        },
        onpass : () => {
          row.classList.remove("valid");
          col[3].innerHTML = "OK";
          iimport.go();
        },
        onfail : msg => {
          row.classList.remove("valid");
          row.classList.add("table-danger");
          col[3].innerHTML = msg;
          iimport.go();
        }
      });
    }

    // (C3) ALL DONE
    else {
      let btn = document.getElementById("sup-items-import-go");
      btn.innerHTML = "Done - Go Back";
      btn.onclick = () => cb.page(1);
      btn.disabled = false;
      items.list(true);
      cb.loading(false);
    }
  }
};