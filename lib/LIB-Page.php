<?php
class Page {
  // (A) DRAW HTML PAGINATION SQUARES
  //  $pgn : pagination data from core paginator()
  //  $action : URL link or Javascript function
  //  $mode : "J"avascript function or "A"nchor links
  //  $adj : number of adjcent page squares
  function draw ($pgn, $action, $mode="J", $adj=1) {
    echo "<ul class='pagination p-3 m-0'>";

    // (A1) ENOUGH PAGES TO HIDE - DRAW WITH ... SQUARES
    if ($pgn["total"]>5 + ($adj*2)) {
      // CURRENT PAGE IS CLOSE TO BEGINNING - HIDE LATER PAGES
      if ($pgn["now"] < 2 + ($adj*2)) {
        for ($i=1; $i<3 + ($adj*2); $i++) {
          $this->cell($i, $action, $mode, $i==$pgn["now"]);
        }
        $this->cell("...");
        for ($i=$pgn["total"]-1; $i<=$pgn["total"]; $i++) {
          $this->cell($i, $action, $mode, $i==$pgn["now"]);
        }
      }

      // CURRENT PAGE SOMEWHERE IN THE MIDDLE
      else if ($pgn["total"] - ($adj*2) > $pgn["now"] && $pgn["now"] > ($adj*2)) {
        for ($i=1; $i<3; $i++) {
          $this->cell($i, $action, $mode, $i==$pgn["now"]);
        }
        $this->cell("...");
        for ($i=$pgn["now"]-$adj; $i<=$pgn["now"]+$adj; $i++) {
          $this->cell($i, $action, $mode, $i==$pgn["now"]);
        }
        $this->cell("...");
        for ($i=$pgn["total"]-1; $i<=$pgn["total"]; $i++) {
          $this->cell($i, $action, $mode, $i==$pgn["now"]);
        }
      }

      // CURRENT PAGE SOMEWHERE IN THE MIDDLE - HIDE EARLY PAGES
      else {
        for ($i=1; $i<3; $i++) {
          $this->cell($i, $action, $mode, $i==$pgn["now"]);
        }
        $this->cell("...");
        for ($i=$pgn["total"] - (2+($adj * 2)); $i<=$pgn["total"]; $i++) {
          $this->cell($i, $action, $mode, $i==$pgn["now"]);
        }
      }
    }

    // (A2) NOT ENOUGH PAGES - JUST DRAW ALL
    else {
      for ($i=1; $i<=$pgn["total"]; $i++) {
        $this->cell($i, $action, $mode, $i==$pgn["now"]);
      }
    }
    echo "</ul>";
  }

  // (B) SUPPORT FUNCTION, DRAW AN HTML PAGINATION CELL
  //  $pg : page number (or text)
  //  $action : URL link or Javascript function
  //  $mode : "J"avascript function or "A"nchor links
  //  $current : is current page?
  function cell ($pg, $action=null, $mode="J", $current=false) {
    // (B1) OPENING <LI> TAG
    echo "<li class='page-item". ($current ? " active" : "") ."'>";

    // (B2) INNER <A> OR <SPAN>
    if ($mode=="A") {
      $tag = "a";
      $act = $action!==null ? " href='$action?pg=$pg'" : "";
    } else {
      $tag = "span";
      $act = $action!==null ? " onclick='$action($pg)'" : "";
    }
    echo "<$tag class='page-link'$act>$pg</$tag>";

    // (B3) CLOSING </LI> TAG
    echo "</li>";
  }
}
