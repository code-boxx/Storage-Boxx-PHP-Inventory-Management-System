var item = {
  // (A) LIST () : SHOW ALL ITEMS
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(1); }
    let data = {
      page : item.pg,
      search : item.find
    };
    if ("NDEFReader" in window) { data.nfc = 1; }
    cb.load({
      page : "items/list", target : "item-list", data : data
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=item.pg) {
    item.pg = pg;
    item.list();
  }},

  // (C) SEARCH ITEMS
  search : () => {
    item.find = document.getElementById("item-search").value;
    item.pg = 1;
    item.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  //  sku : item SKU, for edit only
  addEdit : sku => cb.load({
    page : "items/form", target : "cb-page-2",
    data : { sku : sku ? sku : "" },
    onload : () => cb.page(2)
  }),

  // (E) RANDOM SKU
  // credits : https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
  randomSKU : () => {
    let length = 8, // set your own
        result = "",
        char = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",
        clength = char.length;
    for (let i=0; i<length; i++) {
      result += char.charAt(Math.floor(Math.random() * clength));
    }
    document.getElementById("item-sku").value = result;
  },

  // (F) SAVE ITEM
  save : () => {
    // (F1) GET DATA
    var data = {
      sku : document.getElementById("item-sku").value,
      name : document.getElementById("item-name").value,
      unit : document.getElementById("item-unit").value,
      price : document.getElementById("item-price").value,
      desc : document.getElementById("item-desc").value,
      low : document.getElementById("item-low").value
    };
    var osku = document.getElementById("item-osku").value;
    if (osku!="") { data.osku = osku; }

    // (F2) CALL API
    cb.api({
      mod : "items", act : "save",
      data : data,
      passmsg : "Item saved",
      onpass : item.list
    });
    return false;
  },

  // (G) DELETE ITEM
  //  sku : item SKU
  del : sku => cb.modal(
    `<i class="icon icon-warning"></i> Delete ${sku}?`, 
    `<strong class="text-danger">All movement history for this item will be deleted, item will also be removed from all orders and suppliers.</strong>`,
    () => cb.api({
      mod : "items", act : "del",
      data : { sku : sku },
      passmsg : "Item Deleted",
      onpass : item.list
    })
  ),

  // (H) IMPORT ITEMS
  import : () => im.init({
    name : "ITEMS",
    at : 2, back : 1,
    eg : "dummy-items.csv",
    api : { mod : "items", act : "import" },
    after : () => item.list(true),
    cols : [
      ["SKU", "sku", true],
      ["Item Name", "name", true],
      ["Item Description", "desc", false],
      ["Unit", "unit", true],
      ["Unit Price", "price", "N"],
      ["Watch Level", "low", "N"]
    ]
  }),

  // (I) SUPPLIERS FOR ITEM
  suppg : 1,
  supsku : null,
  sup : sku => cb.load({
    page : "items/sup", target : "cb-page-2",
    data : { sku : sku },
    onload : () => {
      cb.page(2);
      item.suppg = 1;
      item.supsku = sku;
      item.suplist();
    }
  }),
 
  // (J) SUPPLIER LIST FOR ITEM
  suplist : () => cb.load({
    page : "items/sup/list", target : "sup-list",
    data : { 
      sku : item.supsku,
      page : item.suppg
    }
  }),

  // (K) GO TO SUPPLIER PAGE
  suppage : pg => { if (pg!=item.suppg) {
    item.suppg = pg;
    item.suplist();
  }},

  // (L) GENERATE QR CODE
  qr : sku => {
    document.getElementById("qrsku").value = sku;
    document.getElementById("qrform").submit();
  }
};

// (M) INIT MANAGE ITEMS
window.addEventListener("load", () => {
  // (M1) LIST ITEMS
  item.list();

  // (M2) ATTACH AUTOCOMPLETE
  autocomplete.attach({
    target : document.getElementById("item-search"),
    mod : "autocomplete", act : "item",
    onpick : res => item.search()
  });

  // (M3) ATTACH NFC READER
  if (("NDEFReader" in window)) { nfc.init(); }
});