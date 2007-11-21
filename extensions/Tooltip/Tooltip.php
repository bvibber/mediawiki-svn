<?php

$wgExtensionFunctions[] = "wfToolTipExtension";
$wgExtensionCredits['parserhook'][] = array(
    'name'        => 'ToolTip',
    'author'      => 'Paul Grinberg',
    'description' => 'adds <nowiki><tooltip></nowiki> tag',
    'version'     => '0.3'
);

function wfToolTipExtension() {
    global $wgParser;
    $wgParser->setHook( "tooltip", "renderToolTip" );
}


function renderToolTip($input, $argv, &$parser) {
    global $wgToolTip,$wgScriptPath;

    $text = 'see tooltip';
    $xoffset = 0;
    $yoffset = 0;
    foreach ($argv as $key => $value) {
        switch ($key) {
            case 'text':
                $text = $value;
                break;
            case 'x':
                $xoffset = intval($value);
                break;
            case 'y':
                $yoffset = intval($value);
                break;
            default :
                wfDebug( __METHOD__ . ": Requested '$key ==> $value'\n" );
                break;
        }
    }

    $output = '<span>';

    if ($wgToolTip != 1) {
        // This code was borrowed from http://www.texsoft.it/index.php?%20m=sw.js.htmltooltip&c=software&l=it
        $output .= <<< END
<style type="text/css">.xstooltip{visibility: hidden;position: absolute;top: 0;left: 0;z-index: 2;font: normal 8pt sans-serif;padding: 3px;border: solid 1px;background-repeat: repeat;background-image: url($wgScriptPath/images/ttbg.png);}</style><script type= "text/javascript">function xstooltip_findPosX(obj){var curleft = 0;if (obj.offsetParent){while (obj.offsetParent){curleft += obj.offsetLeft;obj = obj.offsetParent;}}else if (obj.x)curleft += obj.x;return curleft - 200;}function xstooltip_findPosY(obj){var curtop = 0;if (obj.offsetParent){while (obj.offsetParent){curtop += obj.offsetTop;obj = obj.offsetParent;}}else if (obj.y)curtop+= obj.y;return curtop - 25}function xstooltip_show(tooltipId, parentId, posX, posY){it = document.getElementById(tooltipId);if (it.style.top == '' || it.style.top == 0){if (it.style.left == '' || it.style.left == 0){it.style.width = it.offsetWidth + 'px';it.style.height = it.offsetHeight + 'px';img = document.getElementById(parentId);x = xstooltip_findPosX(img) + posX;y = xstooltip_findPosY(img) + posY;if (x < 0 ) x = 0;it.style.top = y + 'px';it.style.left = x + 'px';}}it.style.visibility = 'visible';}function xstooltip_hide(id){it = document.getElementById(id);it.style.visibility = 'hidden';}</script>
END;
        $wgToolTip = 1;
    }
    
    $tooltipid = uniqid('tooltipid');
    $parentid = uniqid('parentid');
    $output .= "<div id='$tooltipid' class='xstooltip'><font color=white>" . $parser->unstrip($parser->recursiveTagParse($input),$parser->mStripState) . "</font></div>";
    $output .= "<font color='green'><span id='$parentid' onmouseover=\"xstooltip_show('$tooltipid', '$parentid', $xoffset, $yoffset);\" onmouseout=\"xstooltip_hide('$tooltipid');\">" . $parser->unstrip($parser->recursiveTagParse($text),$parser->mStripState) . "</span></font>";

    return $output . "</span>";
}

?>
