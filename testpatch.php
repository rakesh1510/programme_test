<?php

ini_set("display_errors", 0);

$newfile = file_get_contents("testdoc.txt");
$oldfile = file_get_contents("testdoc_old.txt");

$newexplode = explode("\n", $newfile);
$oldexplode = explode("\n", $oldfile);

//file which consist of large amount line
$largecountline = ( count($newexplode) > count($oldexplode) ) ? count($newexplode) : count($oldexplode);

//mismatched line
for ($l = count($oldexplode); $l < $largecountline; $l++) {
    $oldexplode[$l] = '';
}
// Begin HTML Table
    echo '<table width="100%">', "\n<tbody>\n<tr>\n";

// Begin diff column
    echo '<td valign="top">', "\nCurrent Version:<hr>\n<pre>\n";
for ($f = 0; $f < $largecountline; $f++) {

    //for wor count
    $newwc = explode(" ", $newexplode[$f]);
    $oldwc = explode(" ", $oldexplode[$f]);

    $largewc = (count($newwc) > count($oldwc)) ? count($newwc) : count($oldwc);

    for ($s = count($oldwc); $s < $largewc; $s++) {
        $oldwc[$s] = '';
    }

    if ($newexplode[$f] !== $oldexplode[$f]) {
        for ($w = 0; $w < $largewc; $w++) {
            
            if ($newwc[$w] == $oldwc[$w]) {
                echo $newwc[$w];
                echo ($w != ($largewc - 1)) ? ' ' : "\n";
            } else {
                echo "<b style='color:green'>", $newwc[$w];
                echo ($w != ($largewc - 1)) ? '</b> ' : "</b>\n";
            }
        }
    } else {

        echo $newexplode[$f], "\n";
    }
}
// End diff column
echo "</pre>\n</td>\n<td>&nbsp;</td>\n";
//exit;
// Begin old version column
echo '<td valign="top">', "\nOld Version:<hr>\n<pre>\n";
echo $oldfile, "\n";

// End old version column
echo "</pre>\n</td>\n";

// End HTML table
echo "</tr>\n</tbody>\n</table>";
?>