var check = {
  // (A) LOAD MOVEMENT HISTORY "MAIN PAGE"
  //  sku : string, item sku
  load : function (sku) {
    sb.load({
      page : "icheck",
      target : "sb-page-2",
      data : { sku : sku },
      onload : function () {
        check.sku = sku;
        check.pg = 1;
        sb.page(2);
        check.list();
      }
    });
  },

  // (B) SHOW ITEM MOVEMENT HISTORY
  sku : null, // current item
  pg : 1, // current page
  list : function () {
    sb.load({
      page : "icheck/list",
      target : "i-history",
      data : {
        sku : check.sku,
        page : check.pg
      }
    });
  },

  // (C) GO TO PAGE
  //  pg : int, page number
  goToPage : function (pg) { if (pg!=check.pg) {
    check.pg = pg;
    check.list();
  }},

  // (D) VERIFY VALID SKU BEFORE SHOW HISTORY
  verify : function () {
    var field = document.getElementById("mvt-check");
    sb.api({
      mod : "inventory",
      req : "get",
      data : { sku : field.value },
      passmsg : false,
      onpass : function (res) {
        if (res.data===null) {
          sb.modal("Invalid Item", "SKU is not found in database.");
        } else {
          check.load(field.value);
          field.value = "";
        }
      }
    });
    return false;
  }
};
