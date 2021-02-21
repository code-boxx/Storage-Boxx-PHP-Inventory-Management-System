<?php
class Page {
  // (A) PROPERTIES
  // (A1) PAGINATION CALCULATIONS
  public $pgEntries; // TOTAL NUMBER OF ENTRIES
  public $pgNow; // CURRENT PAGE
  public $pgPer = PG_PER; // NUMBER OF ENTRIES PER PAGE
  public $pgTotal; // TOTAL NUMBER OF PAGES
  public $limX = 0; // CALCULATED LIMIT X,Y
  public $limY = PG_PER; // CALCULATED LIMIT X,Y

  // (A2) HTML PAGINATION SQUARES
  private $pgAction; // URL FOR ANCHOR LINK, OR JS FUNCTION
  private $pgMode = "A"; // "A" FOR ANCHOR LINK, "J" FOR JS ONCLICK
  private $pgAdj = PG_ADJ; // NUMBER OF ADJACENT PAGES FOR PAGINATION SQUARES

  // (B) PRIME () : PRIME THE PAGINATION
  //  $entries : total number of entries
  //  $pgnow : current page number
  //  $per : entries per page
  function prime ($entries, $now=1, $per=PG_PER) {
    // (B1) CALCULATE TOTAL NUMBER OF PAGES
    $this->pgEntries = $entries;
    $this->pgPer = $per;
    $this->pgTotal = CEIL($this->pgEntries / $this->pgPer);

    // (B2) CALCULATE LIMIT X,Y
    $this->pgNow = $now;
    if ($this->pgNow > $this->pgTotal) { $this->pgNow = $this->pgTotal; }
    $this->limX = ($this->pgNow - 1) * $this->pgPer;
    $this->limY = $this->pgPer;
  }

  // (C) LIMIT () : TRAILING LIMIT X,Y FOR SQL - RUN AFTER PRIME()
  function limit () {
    return " LIMIT $this->limX, $this->limY";
  }

  // (D) DATA () : OUTPUT CURRENT PAGINATION CALCULATIONS IN AN ARRAY
  function data () {
    return [
      "pgTotal" => $this->pgTotal,
      "pgNow" => $this->pgNow,
      "pgPer" => $this->pgPer,
      "pgEntries" => $this->pgEntries,
      "limX" => $this->limX,
      "limY" => $this->limY
    ];
  }

  // (E) AUTOGET () : AUTO MAP $_POST TO MODULE FUNCTION
  //  $module : module to load
  //  $count : function to get count of entries
  //  $get : function to get results
  //  $api : formulate API JSON response or return array?
  // NOTE : ASSUMES $_POST['PG'] AS CURRENT PAGE
  // NOTE : ASSUMES $GET HAS A $LIMIT PARAMETER
  function autoGet ($module, $count, $get, $api=false) {
    // (E1) LOAD MODULE
    if (!$this->core->load($module)) { $this->core->respond(0); }

    // (E2) EVIL AUTO GET TOTAL NUMBER OF ENTRIES
    $evil = "\$total = \$this->core->$module->$count(";
    $reflect = new ReflectionMethod($module, $count);
    $params = $reflect->getParameters();
    if (count($params)==0) { $evil .= ");"; }
    else {
      foreach ($params as $p) {
        if (!isset($_POST[$p->name])) { $_POST[$p->name] = null; }
        $evil .= "\$_POST['$p->name'],";
      }
      $evil = substr($evil, 0, -1) . ");";
    }
    eval($evil);

    // (E3) AUTO PRIME PAGINATION
    if (!isset($_POST['pg'])) { $_POST['pg'] = 1; }
    $this->prime($total, $_POST['pg']);

    // (E4) AUTO GET ENTRIES
    $evil = "\$entries = \$this->core->$module->$get(";
    $reflect = new ReflectionMethod($module, $get);
    foreach ($reflect->getParameters() as $p) {
      if ($p->name == "limit") { $evil .= "true,"; }
      else {
        if (!isset($_POST[$p->name])) { $_POST[$p->name] = null; }
        $evil .= "\$_POST['$p->name'],";
      }
    }
    $evil = substr($evil, 0, -1) . ");";
    eval($evil);

    // (E5) RESULTS
    if ($api) {
      $this->core->respond(is_array($entries), null, $entries, $this->data());
    } else {
      return $entries;
    }
  }

  // (F) CELL () : SUPPORT FUNCTION FOR DRAW() - DRAW AN HTML PAGINATION CELL
  //  $pg : current page number
  function cell ($pg) {
    // (F1) OPEN TAG
    if ($this->pgMode=="A") { 
      echo "<a href='".$this->pgAction."pg=$pg'"; 
    } else { 
      echo "<div onclick='$this->pgAction($pg)'"; 
    }

    // (F2) CURRENT PAGE?
    if ($pg==$this->pgNow) { echo " class='current'"; }

    // (F3) PAGE NUMBER
    echo ">$pg";

    // (F4) CLOSE TAG
    echo $this->pgMode=="A" ? "</a>" : "</div>" ;
  }

  // (G) DRAW () : DRAW HTML PAGINATION SQUARES - RUN AFTER PRIME()
  //  $action : URL link or Javascript function
  //  $mode : "A"nchor links or "J"avascript function
  //  $adj : number of adjcent squares
  function draw ($action, $mode="A", $adj=PG_ADJ) {
    // (G1) PAGINATION SETUP
    $this->pgAction = $action;
    $this->pgMode = $mode!="A" && $mode!="J" ? "A" : $mode ;

    // (G2) DRAW PAGINATION SQUARES
    echo '<nav class="simple-pagination">';

    // (G2A) ENOUGH PAGES TO HIDE - DRAW WITH ... SQUARES
    if ($this->pgTotal>5 + ($this->pgAdj*2)) {
      // CURRENT PAGE IS CLOSE TO BEGINNING - HIDE LATER PAGES
      if ($this->pgNow < 2 + ($this->pgAdj*2)) {
        for ($i=1; $i<3 + ($this->pgAdj*2); $i++) { $this->cell($i); }
        echo "<div class='dots'>...</div>";
        for ($i=$this->pgTotal-1; $i<=$this->pgTotal; $i++) { $this->cell($i); }
      }

      // CURRENT PAGE SOMEWHERE IN THE MIDDLE
      else if ($this->pgTotal - ($this->pgAdj*2) > $this->pgNow && $this->pgNow > ($this->pgAdj*2)) {
        for ($i=1; $i<3; $i++) { $this->cell($i); }
        echo "<div class='dots'>...</div>";
        for ($i=$this->pgNow-$this->pgAdj; $i<=$this->pgNow+$this->pgAdj; $i++) { $this->cell($i); }
        echo "<div class='dots'>...</div>";
        for ($i=$this->pgTotal-1; $i<=$this->pgTotal; $i++) { $this->cell($i); }
      }

      // CURRENT PAGE SOMEWHERE IN THE MIDDLE - HIDE EARLY PAGES
      else {
        for ($i=1; $i<3; $i++) { $this->cell($i); }
        echo "<div class='dots'>...</div>";
        for ($i=$this->pgTotal - (2+($this->pgAdj * 2)); $i<=$this->pgTotal; $i++) { $this->cell($i); }
      }
    }

    // (G2B) NOT ENOUGH PAGES - JUST DRAW ALL
    else {
      for ($i=1; $i<=$this->pgTotal; $i++) { $this->cell($i); }
    }
    echo '</nav>';
  }
}