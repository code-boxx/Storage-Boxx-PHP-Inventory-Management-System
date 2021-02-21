var inv = {
  // (A) LIST () : SHOW ALL ITEMS
  pg : 1, // CURRENT PAGE
  find : "", // CURRENT SEARCH
  list : function ()  {
    common.ajax({
      url : urlroot + "inventory-ajax-list",
      target : "inv-list",
      data : {
        pg : inv.pg,
        search : inv.find
      }
    });
  },

  // (B) GOTOPAGE () : GO TO PAGE
  //  pg : page number
  goToPage : function (pg) { if (pg!=inv.pg) {
    inv.pg = pg;
    inv.list();
  }},

  // (C) SEARCH() : SEARCH INVENTORY
  search : function () {
    inv.find = document.getElementById("inv-search").value;
    inv.pg = 1;
    inv.list();
    return false;
  },

  // (D) ADDEDIT () : SHOW ADD/EDIT DOCKET
  // sku : item SKU, for edit only
  addEdit : function (sku) {
    common.ajax({
      url : urlroot + "inventory-ajax-form",
      data : { sku : sku ? sku : "" },
      target : "pageB",
      onpass : function () { common.page("B"); }
    });
  },

  // (E) RANDOMSKU () : RANDOM SKU
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
  
  // (F) UNIT () : SET UNIT OF MEASUREMENT
  unit : function (u) {
    document.getElementById("inv-unit").value = u;
  },

  // (G) SAVE () : SAVE ITEM
  save : function () {
    // (G1) GET DATA
    var data = {
      reqA : "save",
      sku : document.getElementById("inv-sku").value,
      name : document.getElementById("inv-name").value,
      unit : document.getElementById("inv-unit").value,
      desc : document.getElementById("inv-desc").value
    };
    var osku = document.getElementById("inv-osku").value;
    if (osku!="") { data.osku = osku; }

    // (G2) AJAX
    common.ajax({
      url : urlapi + "Inventory",
      data : data,
      apass : "Item save OK",
      onpass : function () {
        inv.list();
        common.page('A');
      }
    });
    return false;
  },
  
  // (H) DEL () : DELETE ITEM
  //  sku : item SKU
  del : function (sku) { if (confirm(`Delete ${sku}?`)) {
    common.ajax({
      url : urlapi + "Inventory",
      data : {
        reqA : "del",
        sku : sku
      },
      apass : "Item deleted",
      onpass : function () {
        inv.list();
        common.page('A');
      }
    });
  }},

  // (I) BARCODE () : GENERATE BAR CODE
  barcode : function (sku) {
    window.open(urlroot + "barcode/?sku="+sku);
  }
};
window.addEventListener("load", inv.list);