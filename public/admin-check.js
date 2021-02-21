var check = {
  // (A) HTML ITEM DETAILS + HISOTRY
  iwrap : null,
  ihist : null,
  
  // (B) SHOW ITEM DETAILS + HISTORY
  show : function () {
    common.ajax({
      url : urlapi + "Inventory",
      data : {
        reqA : "get",
        sku : document.getElementById("mvt-sku").value
      },
      apass : false,
      onpass : function (res) {
        if (res.data === false) {
          check.iwrap.innerHTML = "<div class='bar'>INVALID SKU</div>";
          check.ihist.innerHTML = "";
        } else {
          var ihtml = `<div class='bar'>[${res.data['stock_sku']}] ${res.data['stock_name']}<br>`;
          ihtml += `<small>${res.data['stock_desc']}</small><br>`;
          ihtml += `<small>${res.data['stock_qty']} ${res.data['stock_unit']}</small></div>`;
          check.iwrap.innerHTML = ihtml;
          check.sku = res.data['stock_sku'];
          check.pg = 1;
          check.history();
        }
      }
    });
    return false;
  },

  // (B) MOVEMENT HISTORY PAGE
  sku : null, pg : null,
  gotopg : function (pg) { if (pg!=check.pg) {
    check.pg = this.value;
    check.history();
  }},

  // (C) SHOW ITEM MOVEMENT HISTORY
  history : function () {
    common.ajax({
      url : urlroot + "check-ajax-history",
      target : "mvt-history",
      data : {
        pg : check.pg,
        sku : check.sku
      }
    });
  }
};

// (D) GET HTML ELEMENTS + AUTOCOMPLETE SKU
window.addEventListener("load", function(){
  check.iwrap = document.getElementById("mvt-item");
  check.ihist = document.getElementById("mvt-history");
  ac.attach({
    target : "mvt-sku",
    data : urlapi + "Inventory",
    post : { reqA : "findSKU" }
  });
});