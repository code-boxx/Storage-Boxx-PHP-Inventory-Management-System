var inv = {
  // (A) LIST () : SHOW ALL ITEMS
  pg : 1, // CURRENT PAGE
  find : "", // CURRENT SEARCH
  list : function ()  {
    sb.page(1);
    sb.load({
      page : "inventory/list",
      target : "inv-list",
      data : {
        page : inv.pg,
        search : inv.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : function (pg) { if (pg!=inv.pg) {
    inv.pg = pg;
    inv.list();
  }},

  // (C) SEARCH INVENTORY
  search : function () {
    inv.find = document.getElementById("inv-search").value;
    inv.pg = 1;
    inv.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  // sku : item SKU, for edit only
  addEdit : function (sku) {
    sb.load({
      page : "inventory/form",
      target : "sb-page-2",
      data : { sku : sku ? sku : "" },
      onload : function () { sb.page(2); }
    });
  },

  // (E) RANDOM SKU
  // Credits : https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
  randomSKU : function () {
    let length = 8, // SET YOUR OWN
        result = "",
        char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        clength = char.length;
    for (let i=0; i<length; i++) {
      result += char.charAt(Math.floor(Math.random() * clength));
    }
    document.getElementById("inv-sku").value = result;
  },

  // (F) SET UNIT OF MEASUREMENT
  unit : function (u) {
    document.getElementById("inv-unit").value = u;
  },

  // (G) SAVE ITEM
  save : function () {
    // (G1) GET DATA
    var data = {
      sku : document.getElementById("inv-sku").value,
      name : document.getElementById("inv-name").value,
      unit : document.getElementById("inv-unit").value,
      desc : document.getElementById("inv-desc").value
    };
    var osku = document.getElementById("inv-osku").value;
    if (osku!="") { data.osku = osku; }

    // (G2) AJAX
    sb.api({
      mod : "inventory",
      req : "save",
      data : data,
      passmsg : "Item saved",
      onpass : inv.list
    });
    return false;
  },

  // (H) DELETE ITEM
  //  sku : item SKU
  //  confirm : boolean, confirmed delete
  del : function (sku, confirm) {
    if (confirm) {
      sb.api({
        mod : "inventory",
        req : "del",
        data : { sku : sku },
        passmsg : "Item Deleted",
        onpass : inv.list
      });
    } else {
      sb.modal("Please confirm", `Delete ${sku}? All movement history will be lost!`, function(){
        inv.del(sku, true);
      });
    }
  },

  // (I) GENERATE BAR CODE
  barcode : function (sku) {
    window.open(sbhost.base + "a/barcode/?sku="+sku);
  }
};
window.addEventListener("load", inv.list);
