<?php

ini_set("display_errors", 0);
// Display source code
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    if (isset($_GET['source'])) {
        header('Content-type: text/plain; charset=UTF-8');
        exit(file_get_contents(basename(__FILE__)));
    }
}

// Replace '<' and '>' Characters
function replace($input) {
    return str_replace(array('<', '>'), array('&lt;', '&gt;'), $input);
}

// Read files
$cf = file_get_contents('testdoc.txt'); // Current Version
$of = file_get_contents('testdoc_old.txt'); // Old Version
//print_r($of);
//print_r($cf);
//exit;
// Line Arrays
$cv = explode("\n", $cf);
$ov = explode("\n", $of);
//echo "<pre>";
//echo "NEW";
//print_r($cv);
//echo "OLD";
//print_r($ov);
// Count Lines - Set to Longer Version
$lc = (count($cv) > count($ov)) ? count($cv) : count($ov);
//echo "NEW----";
//echo count($cv) . "---";
//echo "OLD---";
//echo count($ov);
//exit;
// Fix Mismatched Line Counts
for ($flc = count($ov); $flc < $lc; $flc++) {
    $ov[$flc] = '';
}
//echo "OLD";
//print_r($ov);
//exit;
// Begin HTML Table
echo '<table width="100%">', "\n<tbody>\n<tr>\n";

// Begin diff column
echo '<td valign="top">', "\nCurrent Version:<hr>\n<pre>\n";

for ($l = 0; $l < $lc; $l++) {
    // Word Arrays
    $cw = array();
    $ow = array();
    $cw = explode(' ', $cv[$l]); // Current Version
    $ow = explode(' ', $ov[$l]); // Old Version
//    echo "NEW----" . print_r($cw);
//    echo "OLD---" . print_r($ow);
//    exit;
    // Count Words - Set to Longer Version
    $wc = (count($cw) > count($ow)) ? count($cw) : count($ow);

    // Fix Mismatched Word Counts
    for ($fwc = count($ow); $fwc < $wc; $fwc++) {
        $ow[$fwc] = '';
    }

    // If each line is identical, just echo the normal line. If not,
    // check if each word is identical. If not, wrap colored "<b>"
    // tags around the mismatched words.
//    echo "CVL===>" . $cv[$l] . "<br/>";
//    echo "OVL===>" . $ov[$l] . "<br/>";
    $w = 0;
    if ($cv[$l] !== $ov[$l]) {
        for ($w = 0; $w < $wc; $w++) {
            if ($cw[$w] === $ow[$w]) {
                echo $cw[$w];
//                echo "WWWWWWWWWW-->".$w."<br/>";
//                echo "Wc-1-->".($wc - 1)."<br/>";

                echo ($w !== ($wc - 1)) ? ' ' : "\n";
            } else {
//                echo "teste";
                echo '<b style="color: #00BB00;">', $cw[$w];
                echo ($w !== ($wc - 1)) ? '</b> ' : "</b>\n";
            }
        }
    } else {
        echo $cv[$l], "\n";
    }
}

    // End diff column
    echo "</pre>\n</td>\n<td>&nbsp;</td>\n";
    //exit;
    // Begin old version column
    echo '<td valign="top">', "\nOld Version:<hr>\n<pre>\n";
    echo $of, "\n";

    // End old version column
    echo "</pre>\n</td>\n";

    // End HTML table
    echo "</tr>\n</tbody>\n</table>";
