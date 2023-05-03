var inv = {
  // (A) SUPPLIERS FOR ITEM
  suppg : 1,
  supsku : null,
  sup : sku => cb.load({
    page : "inventory/sup", target : "cb-page-2",
    data : { sku : sku },
    onload : () => {
      cb.page(2);
      inv.suppg = 1;
      inv.supsku = sku;
      inv.suplist();
    }
  }),

  // (B) SUPPLIER LIST FOR ITEM
  suplist : () => cb.load({
    page : "inventory/sup/list", target : "sup-list",
    data : { 
      sku : inv.supsku,
      page : inv.suppg
    }
  }),

  // (C) GO TO SUPPLIER PAGE
  suppage : pg => { if (pg!=inv.suppg) {
    inv.suppg = pg;
    inv.suplist();
  }}
};