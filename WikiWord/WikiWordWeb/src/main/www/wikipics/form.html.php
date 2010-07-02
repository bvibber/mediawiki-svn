<?php 
if (!defined("WIKIPICS")) die("not a valid entry point");

?>
<div class="inputform" >
    <form name="search" action="<?php print $wwSelf; ?>">
      <table border="0" class="inputgrid" summary="input form">
	<tr>
	  <td>
	    <?php 
	      $u = $utils->getThumbnailURL("Commons_logo_optimized.svg", 60); 
	      print "<img class=\"logo\" alt=\"Wikimedia Commons Logo\" src=\"".htmlspecialchars($u)."\" title=\"Search Wikimedia Commons\" align=\"bottom\"/>";
	    ?>
	  </td>
	  <td>
	    <label for="term" style="display:none">Term: </label><input type="text" name="term" id="term" size="24" value="<?php print htmlspecialchars($term); ?>"/>
	  </td>
	  <td>
	    <label for="term" style="display:none">Language: </label>
	    <?php WWUtils::printSelector("lang", $wwLanguages, $lang) ?>
	  </td>
	  <td>
	    <input type="submit" name="go" value="go"/>
	  </td>
	  <td width="100%">
	    &nbsp;
	  </td>
	</tr>
	<tr>
	  <td class="note" colspan="5">
	    <small>Note: this is a thesaurus lookup, not a full text search. Multiple words are handeled as a single phrase. Only exact matches of complete phrases will be found. </small>
	  </td>
	</tr>
      </table>
      
      <?php
      if ( @$debug ) {
	      print '<input type="hidden" name="debug" value="true"/>';
	      print "<p class='debug'>debug mode enabled!</p>";
	      flush();                           
      }
      ?>
    </form>
    </div>
