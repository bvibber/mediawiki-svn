<?php 
if (!defined("WIKIPICS")) die("not a valid entry point");

function printConceptList($langs, $concepts, $class, $limit = false) {
    if (!$concepts) return false;

    if (!$limit || $limit > count($concepts)) $limit = count($concepts);

    $i = 0;
    ?>
    <ul class="<?php print $class; ?>">
      <?php
	foreach ($concepts as $c) {
	    $link = getConceptDetailsLink($langs, $c);
	    if (!$link) continue;

	    ?><li><?php
	    print $link;
	    
	    $i += 1;
	    if ($i >= $limit) break;
	    ?></li><?php
	}
      ?>
    </ul>
    <?php

    return $i < count($concepts);
}

function printConceptImageList($concept, $terse = false, $columns = 5, $limit = false ) {
    global $utils, $wwThumbSize;

    if (!$concept) return false;

    if (is_array($concept) && !isset($concept['id'])) $images = $concept; #XXX: HACK
    else $images = getImagesAbout($concept, $max ? $max +1 : false);

    if (!$images) return;

    $class = $terse ? "terseImageTable" : "";

    $imgList = array_values($images);

    $cw = $wwThumbSize + 32; //FIXME: magic number, use config!

    ?>
    <table class="imageTable <?php print $class; ?>" summary="images" width="<?php print $columns*$cw; ?>">
      <colgroup span="<?php print $columns; ?>" width="<?php print $cw; ?>">
      </colgroup>

      <?php
	$i = 0;
	$c = count($images);
	if (!$limit || $limit > $c) $limit = $c;

	while ($i < $limit) {
	  $i = printConceptImageRow($imgList, $i, $terse, $columns, $limit);
	}
      ?>
    </table>
    <?php

    return $i < $c;
}

function printConceptImageRow($images, $from, $terse, $columns = 5, $limit = false) {
	global $wwThumbSize, $utils;

	$cw = $wwThumbSize + 32; //FIXME: magic number, use config!
	$cwcss = $cw . "px";

	$to = $from + $columns;
	if ( $to > $limit ) $to = $limit;

	print "\t<tr class=\"imageRow\">\n";
	
	for ($i = $from; $i<$to; $i += 1) {
	  $img = $images[$i];
	  print "\t\t<td class=\"imageCell\" width=\"$cw\" align=\"left\" valign=\"bottom\" nowrap=\"nowrap\" style=\"width: $cwcss\"><div class=\"clipBox\" style=\"width:$cwcss; max-width:$cwcss;\">";
	  print $utils->getThumbnailHTML($img, $wwThumbSize, $wwThumbSize);
	  print "</div></td>\n";
	}
	
	print "\n\t</tr>\n";

	if (!$terse) {
	    print "\t<tr class=\"imageMetaRow\">\n";
	    
	    for ($i = $from; $i<$to; $i += 1) {
	      $img = $images[$i];

	      $title = $img['name'];
	      $title = str_replace("_", " ", $title);
	      $title = preg_replace("/\\.[^.]+$/", "", $title);

	      $info = getImageInfo($img);
	      $labels = getImageLabels($img);

	      print "\t\t<td class=\"imageMetaCell\" width=\"$cw\" align=\"left\" valign=\"top\" style=\"width: $cwcss\"><div class=\"clipBox\" style=\"width:$cwcss; max-width:$cwcss;\">";
	      print "<div class=\"imageTitle\" title=\"" . htmlspecialchars( $img['name'] ) . "\">" . htmlspecialchars( $title ) . "</div>";

	      if ($info) {
		  print "<div class=\"imageInfo\">";
		  printList($info, false, "terselist");
		  print "</div>";
	      }

	      if ($labels) {
		  print "<div class=\"imageLabels\">";
		  printList($labels, false, "terselist");
		  print "</div>";
	      }

	      print "</div></td>\n";
	    }
	    
	    print "\n\t</tr>\n";
	}

	return $to;
}

function getConceptDetailsLink($langs, $concept, $text = NULL) {
    global $utils;

    $name = $utils->pickLocal($concept['name'], $langs);
    if ( $name === false || $name === null || $name === "") return false;

    $name = str_replace("_", " ", $name);
    $score = @$concept['score'];

    if ($text === null) $text = $name;
    if ($text === null || $text === false || $text === "") return false;
  
    $u = getConceptDetailsURL($langs, $concept);
    return '<a href="' . htmlspecialchars($u) . '" title="' . htmlspecialchars($name) . ' (score: ' . (int)$score . ')'. '">' . htmlspecialchars($text) . '</a>';
}

function getConceptPageLinks($lang, $concept) {
    $urls = getConceptPageURLs($lang, $concept);
    if (!$urls) return false;

    foreach ($urls as $page => $u) {
	$links[] = '<a href="' . htmlspecialchars($u) . '" title="' . htmlspecialchars( str_replace("_", " ", $page) ) . '">' . htmlspecialchars( $lang . ":" . str_replace("_", " ", $page) ) . '</a>';
    }

    return $links;
}

function getAllConceptPageLinks($concept) {
    $links = array();

    foreach ( $concept['languages'] as $lang ) {
	$ll = getConceptPageLinks($lang, $concept);
	if ($ll) $links[$lang] = $ll;
    }

    return $links;
}

function printList($items, $escape = true, $class = "list") {
    ?>
    <ul class="<?php print htmlspecialchars($class); ?>">
      <?php
	foreach ($items as $item) {
	    if ( !$item ) continue;
	    if ( $escape ) $item = htmlspecialchars($item);
	    print "<li>" . trim($item) . "</li>";
	}
      ?>
    </ul>
    <?php
}

function printConceptPageList( $langs, $concept, $class, $limit = false ) {
    $linksByLanguage = getAllConceptPageLinks($concept);

    $i = 0;
    $more = false;
    ?>
    <ul class="<?php print htmlspecialchars($class); ?>">
      <?php
	foreach ( $linksByLanguage as $lang => $links ) {
	    foreach ($links as $link ) {
		print "<li>" . trim($link) . "</li>";
	    
		$i += 1;
		if ($limit && $i >= $limit) break;
	    }

	    if ($limit && $i >= $limit) {
		$more = true;
		break;
	    }
	}
      ?>
    </ul>
    <?php

    return $more;
}


function printDefList($items, $scapeKeys = true, $escapeValues = true, $class = "list") {
    ?>
    <dl class="<?php print htmlspecialchars($class); ?>">
      <?php
	foreach ($items as $key => $item) {
	    if ( $escapeKeys ) $key = htmlspecialchars($key);
	    print "\t\t<dt>" . $key . "</dt>\n";

	    if ( $escapeValues ) $item = htmlspecialchars($item);
	    print "\t\t\t<dd>" . $item . "</dd>\n";
	}
      ?>
    </sl>
    <?php
}

function getWeightClass($weight) {
    if (!isset($weight) || !$weight) { 
      return "unknown";
      $weight = NULL;
    }
    else if ($weight>1000) return "huge";
    else if ($weight>100) return "big";
    else if ($weight>10) return "normal";
    else if ($weight>2) return "some";
    else return "little";
}


function printConcept($concept, $langs, $terse = true) {
    global $utils, $wwMaxPreviewImages, $wwMaxGalleryImages, $wwMaxPreviewLinks, $wwMaxDetailLinks, $wwGalleryColumns;

    extract( $concept );
    if (@$score) $wclass = getWeightClass($score);
    else $wclass = "";

    #$lclass = $terse ? "terselist" : "list";
    $lclass = "terselist";

    $name = $utils->pickLocal($concept['name'], $langs);
    $name = str_replace("_", " ", $name);

    $gallery = getImagesAbout($concept, $terse ? $wwMaxPreviewImages*2 : $wwMaxGalleryImages+1 );

    if (empty($definition)) $definition = "";
    else if (is_array($definition)) $definition = $utils->pickLocal($definition, $langs);

    ?>
    <tr class="row_head">
      <td colspan="3">
	  <h1 class="name <?php print "weight_$wclass"; ?>"><?php print getConceptDetailsLink($langs, $concept); ?>:</h1>
	   <p class="definition"><?php print htmlspecialchars($definition); ?></p> 
	  <div class="wikipages"><?php printConceptPageList( $langs, $concept, $lclass, $terse ? $wwMaxPreviewLinks : $wwMaxDetailLinks ); ?></div>
	  <strong class="more">[<?php print getConceptDetailsLink($langs, $concept, "details..."); ?>]</strong>
      </td>
    </tr>

    <tr class="row_images">
      <td class="cell_images" colspan="2">
      <?php 
	  if (!$gallery) print "<p class=\"notice\">No images found for concept <em>".htmlspecialchars($name)."</em>.</p>";
	  else $more = printConceptImageList( $gallery, $terse, $wwGalleryColumns, $terse ? $wwMaxPreviewImages : $wwMaxGalleryImages ); 
      ?>
      </td>
    </tr>

    <?php if ($gallery && $terse && $more) { ?>
    <tr class="row_images row_more_images">
      <td class="cell_more_images" colspan="3" width="100%" style="vertical-align:bottom; padding: 1ex; font-size:normal;">
      <?php print " <div><strong class=\"more\">[" . getConceptDetailsLink($langs, $concept, "more images...") . "]</strong></div>"; ?>
      </td>
    </tr>
    <?php } ?>

    <?php if (!$terse && @$concept['narrower']) { ?>
    <tr class="row_narrower">
      <td class="cell_related" colspan="3">
      <strong class="label">Narrower:</strong>
      <?php 
	  $more = printConceptList( $langs, $concept['narrower'], $lclass, $terse ? $wwMaxPreviewLinks : $wwMaxDetailLinks ); 
      ?>
      <?php if ($terse && $more) print " <strong class=\"more\">[" . getConceptDetailsLink($langs, $concept, "more...") . "]</strong>"; ?>
      </td>
    </tr>
    <?php } ?>

    <?php if (!$terse && @$concept['narrower']) { ?>
    <?php 
      $related = getRelatedConceptList($concept);
      if ($related) { 
    ?>
    <tr class="row_related">
      <td class="cell_related" colspan="3">
      <strong class="label">Related:</strong> 
      <?php 
	  $more = printConceptList( $langs, $related, $lclass, $terse ? $wwMaxPreviewLinks : $wwMaxDetailLinks ); 
      ?>
      <?php if ($terse && $more) print " <strong class=\"more\">[" . getConceptDetailsLink($langs, $concept, "more...") . "]</strong>"; ?>
      </td>
    </tr>
    <?php } ?>
    <?php } ?>

    <?php if (!$terse && @$concept['broader']) { ?>
    <tr class="row_category">
      <td class="cell_related" colspan="3">
      <strong class="label">Broader:</strong>
      <?php 
	  $more = printConceptList( $langs, $concept['broader'], $lclass, $terse ? $wwMaxPreviewLinks : $wwMaxDetailLinks ); 
      ?>
      <?php if ($terse && $more) print " <strong class=\"more\">[" . getConceptDetailsLink($langs, $concept, "more...") . "]</strong>"; ?>
      </td>
    </tr>
    <?php } ?>

    <tr class="row_blank">
      <td class="cell_blank" colspan="3">
      &nbsp;
      </td>
    </tr>

    <?php
    if (isset($score) && $score && $score<2 && $pos>=3) return false;
    else return true;
}

if (!isset($scriptPath)) $scriptPath = "./";
if (!isset($skinPath)) $skinPath = "$scriptPath/../skin/";

header("Content-Type: text/html; charset=UTF-8");
debug("starting HTML output");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WikiPics: Multilingual Search for Wikimedia Commons</title>
    <link rel="stylesheet" href="<?php print "$skinPath/styles.css"; ?>" type="text/css" media="all" />
    <link rel="search"
           type="application/opensearchdescription+xml" 
           href="<?php print "$scriptPath/opensearch_description.xml"; ?>"
           				title="Image Search" />
</head>
<body>
    <div class="header">
      <div style="float:left"><?php print WIKIPICS_VERSION; ?></div>
      <div style="float:right"><a href="http://wikimedia.de">Wikimedia Deutschland e.V.</a></div>
    <!--   <h1>WikiWord Navigator</h1>
      <p>Experimental semantic navigator and thesaurus interface for Wikipedia.</p>
      <p>The WikiWord Navigator was created as part of the WikiWord project run by <a href="http://wikimedia.de">Wikimedia Deutschland e.V.</a>.
      It is based on a <a href="http://brightbyte.de/page/WikiWord">diploma thesis</a> by Daniel Kinzler, and runs on the <a href="http://toolserver.org/">Wikimedia Toolserver</a>. WikiWord is an ongoing research project. Please contact <a href="http://brightbyte.de/page/Special:Contact">Daniel Kinzler</a> for more information.</p>  --> &nbsp;
    </div>

    <?php include("form.html.php"); ?>
<?php
debug("-1-");
if ($error) {
  print "<p class=\"error\">".htmlspecialchars($error)."</p>";
}

debug("-2-");
if (!$result && $mode) {
  if ($mode=="concept") print "<p class=\"notice\">Concept not found: <em>".htmlspecialchars($lang).":$".htmlspecialchars($concept)."</em></p>";
  else if ($mode=="term") print "<p class=\"notice\">No meanings found for term <em>".htmlspecialchars($lang).":".htmlspecialchars($term)."</em>.</p>";
}
?>    

<?php
debug("-3-");
if ($result && $mode) {
    debug("-4-");
    if ( $mode == 'concept' ) $terse = false;
    else if ( $mode == 'term' ) $terse = true;
    debug("-5-");
?>
    <table  border="0" class="results" cellspacing="0" summary="search results">
<?php
    debug("-6-");
    debug("processing results");

    $count = 0;
    foreach ( $result as $row ) {
	$count = $count + 1;
	$row['pos'] = $count;

?>    
    <?php 
	  if ( @$debug ) {
		  print "<p class='debug'>procesing concept #".htmlspecialchars($row['id'])."</p>";
		  flush();                           
	  }

	  mangleConcept($row);
	  $continue= printConcept($row, $languages, $terse);
	  flush();

	  if (!$continue) break;
	  if ($limit && $count >= $limit) break;
    ?>

<?php
    } #concept loop

?>
    </table>
<?php
} #if results
?>

<div class="footer">
<p>Wikipics is provided by <a href="http://wikimedia.de">Wikimedia Deutschland</a> as part of the <a href="http://brightbyte.de/page/WikiWord">WikiWord</a> project.</p>

</div>
</body>
<?php
foreach ( $profiling as $key => $value ) {
  print "<!-- $key: $value sec -->\n";
}
?>
</html>

