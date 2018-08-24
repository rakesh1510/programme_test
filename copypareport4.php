<?php
if ($_GET['export']) {
    header("content-type: application/vnd.ms-excel");
    header('Content-Disposition: attachement; filename="reportbydelegations.xls"');
} else {
    include './inc/constant.php';
//    include "dynmenu.php";
}
$server = $_SERVER['SERVER_NAME'];
echo "<script type='text/javascript' src='http://$server/inc/tablesort.js'></script>";
echo "<script type='text/javascript' src='http://$server/inc/jquery.js'></script>";
echo "<script type='text/javascript' src='http://$server/inc/jquery.tablesorter.min.js'></script>";
?>

<script>
    var newwindow;
    function poptastic(url)
    {
        newwindow = window.open(url, 'name', 'height=300,width=900,scrollbars=yes');
        if (window.focus) {
            newwindow.focus()
        }
    }
    $(document).ready(function ()
    {
        $("#rowspan").tablesorter({
            debug: false
        });
    }
    );
</script>


<?php
$submitter = $_COOKIE['authdownload'];

//include './inc/constant.php';
//include "inc/include.php";
$queryadmin = mysql_query("select * from admins where sgid='$submitter'");
if (mysql_num_rows($queryadmin) > 0) {
    $admin = 1;
    $refresh = 0;
}
$ip = $_SERVER['REMOTE_ADDR'];
if ($ip == '127.0.0.1')
    $flagz = 1;
else {
    $acc = mysql_query("SELECT access from ad_transaction_access_denied where currentDate = curdate()", $db) or die(mysql_error());
    while ($a = mysql_fetch_row($acc)) {
        $flagz = $a[0];
    }
    if ($flagz == null)
        $flagz = 1;
}

if ($flagz == '3') { // we are currently working
    include "wip1.php";
} elseif ($flagz == '0') { // data refresh in progress
    include "wip2.php";
} elseif ($flagz == '1') { //give access
    $submitter = $_COOKIE['authdownload'];
    $admin = 0;
    include "inc/include.php";
    $queryadmin = mysql_query("select * from admins where sgid='$submitter'");
    if (mysql_num_rows($queryadmin) > 0) {
        $admin = 1;
    }
    if ($_GET['total']) {
        $total = $_GET['total'];
    }
    if ($_GET['counts']) {
        $counts = $_GET['counts'];
    }
    if ($total == 1) {
        $nexttotal = 0;
        $labeltotal = "Hide fully patched systems";
    } else {
        $nexttotal = 1;
        $labeltotal = "Show fully patched systems";
    }
    if ($counts == 1) {
        $rowspan = 3;
        $colspan = 2;
        $colspan2 = 4;
        $label = "Hide # of computers";
        $label2 = 0;
    } else {
        $rowspan = 2;
        $colspan = 1;
        $colspan2 = 2;
        $label = "Show # of computers";
        $label2 = 1;
    }
    $months = time() - ($days * 24 * 60 * 60);
    $time = number_format((date("Ymd000000", $months)), 0, ',', '');
    $timead = number_format((date("Ymd", $months)), 0, ',', '');

    $target = 104000;
    $db = mysql_connect($HOSTNAME, $USER, $PASS) or die('No Connection');
    $db2 = mysql_select_db($DB, $db);
    if ($_GET['deleg'] == '%') {
        $_GET['deleg'] = '';
        $_GET['dc'] = '';
        $_GET['ou'] = '';
        $_GET['bu'] = '';
        $_GET['site'] = '';
    }
    if (($_GET['dc'] == '%') && ($_GET['ou'] == '%')) {
        $_GET['dc'] = '';
        $_GET['ou'] = '';
    }
    if (($_GET['deleg']) && ($_GET['dc']) && ($_GET['ou']) && ($_GET['bu'] != '%')) {
        $type = 'site';
        $deleg = $_GET['deleg'];
        $dc = $_GET['dc'];
        $ou = str_replace("$", "&", str_replace("*", " ", $_GET['ou']));
        $bu = str_replace("$", "&", str_replace("*", " ", $_GET['bu']));
        $nbadinact = mysql_query("SELECT distinct ad_site,count(ad_name) from fullreport where ad_inactive=0 and delegation_id = '$deleg' and ad_dc='$dc' and ad_ou='$ou' and ad_bu='$bu' and ad_os1 like 'W%' and ad_os1 like '%server%' group by ad_site having count(ad_name) > 0 order by ad_site asc", $db) or die(mysql_error());
    } elseif (($_GET['deleg']) && ($_GET['dc']) && ($_GET['ou'])) {
        $type = 'bu';
        $deleg = $_GET['deleg'];
        $dc = $_GET['dc'];
        $ou = str_replace("$", "&", str_replace("*", " ", $_GET['ou']));
        $nbadinact = mysql_query("SELECT distinct ad_bu,count(ad_name) from fullreport where ad_inactive=0 and delegation_id = '$deleg' and ad_dc='$dc' and ad_ou='$ou' and ad_os1 like 'W%' and ad_os1 like '%server%' group by ad_bu having count(ad_name) > 0 order by ad_bu asc", $db) or die(mysql_error());
    } elseif (($_GET['deleg']) && (!$_GET['dc']) && (!$_GET['ou'])) {
        $type = 'ou';
        $deleg = $_GET['deleg'];
        $nbadinact = mysql_query("SELECT distinct ad_ou,ad_dc,count(ad_name) from fullreport where ad_inactive=0 and delegation_id = '$deleg' and ad_os1 like 'W%' and ad_os1 like '%server%' group by ad_dc,ad_ou having count(ad_name) > 0 order by ad_ou asc", $db) or die(mysql_error());
        $searchdelegname = mysql_query("select delegations.delegation from delegations where id= '$deleg'") or die(mysql_error());
        $delegname = mysql_fetch_row($searchdelegname);
        if (substr($delegname[0], 0, strlen($sgtsprefix)) == $sgtsprefix) {
            $delegname[0] = substr($delegname[0], strlen($sgtsprefix), strlen($delegname[0]));
        }
    } else {
        $type = 'deleg';
        if ($admin == 1) {
            $nbadinact = mysql_query("SELECT delegation_name,delegation_id from fullreport where ad_inactive=0  group by delegation_name order by delegation_name  asc", $db) or die(mysql_error());
        } else {
            $nbadinact = mysql_query("SELECT delegation_name,delegation_id from fullreport where ad_inactive=0 and delegation_name not like 'Hidden - %' and delegation_name not like 'DSI Groupe%' group by delegation_name order by delegation_name  asc", $db) or die(mysql_error());
        }
    }

    $dclink = str_replace("&", "$", str_replace(" ", "*", $dc));
    $sitelink = str_replace(" ", "*", $site);
    $bulink = str_replace("&", "$", str_replace(" ", "*", $bu));
    $oulink = str_replace("&", "$", str_replace(" ", "*", $ou));
    switch ($type) {
        case 'deleg':
            $localisation = 'by Delegations';
            $libelle = 'Delegations / SGTS';
            break;
        case 'ou':
            if (substr($delegname[0], 0, 4) == 'SGTS') {
                $localisation = 'for ' . $delegname[0];
                $libelle = 'Countries / OUs';
            } else {
                $localisation = 'for Delegation ' . $delegname[0];
                $libelle = 'OUs';
            }
            break;
        case 'bu':
            if (substr($ou, 0, 1) == 'C') {
                $localisation = 'for OU ' . $dc . '\\' . $ou;
                $libelle = 'Business Units';
            } else {
                $localisation = 'for OU ' . $dc . '\\' . $ou;
                $libelle = 'Sub OUs';
            }
            break;
        case 'site':
            if (substr($ou, 0, 1) == 'C') {
                $localisation = 'for Business Unit "' . $bu . '" in OU ' . $dc . '\\' . $ou;
                $libelle = 'SGT Sites';
            } else {
                $localisation = 'for Organisational Unit "' . $bu . '" in OU ' . $dc . '\\' . $ou;
                $libelle = 'Sub OUs';
            }
            break;
    }

    $time2 = date('F dS Y H:i');
    $title = 'Security Patches Deployment ' . $localisation;
    echo '<center><font size=4  face=Verdana><b>' . $title . '</font></b><br><font size=2  face=Verdana>' . $time2 . ' (Paris Time)<br></font></center><br>';
    echo '<center><font size=4  face=Verdana><b>Because of a Policy Auditor agent issue higly escalated at McAfee side, security Patches for Servers for September 2017 are not displayed.<br>We are working with high priority to be able to provide data as soon as possible.</b></font></center><br>';
    echo "<h3>(Sources: Policy Auditor / AD for computers seen in the last 30 days)<br></h3>";
    $querynbpatchs = mysql_query("select count(distinct MSREF) from patchref", $db);
    $nbpatchs = mysql_fetch_row($querynbpatchs);

    echo "<table id='rowspan' class=Design2>";
    echo "<thead><tr><th rowspan='' class='' colspan='4'><center>Delegation</center></th>";

    $query2 = mysql_query("select MSREF,MSBulletin,SGBulletin,osserv,type from patchref where type='KB' and osserv not like '' order by SGBulletin desc,MSBulletin desc", $db) or die("Cannot get patch list " . mysql_error());
    while ($result2 = mysql_fetch_row($query2)) {

        if ($counts == 0) {
            if ($result2[0] == 'sms') {
                echo "<td colspan='";
                echo sizeof(explode(',', $result2[3])) - 1;
                echo "'>Active Directory<br>Scope</td>";
            }

            echo "<td colspan='";
            echo sizeof(explode(',', $result2[3])) - 1;
            echo "'>";
        } else {
            if ($result2[0] == 'sms') {
                echo "<td colspan=";
                echo number_format(sizeof(explode(',', $result2[3])), 0);
                echo ">Active Directory Scope</td>";
            }
            echo "<td colspan=";
            echo number_format(sizeof(explode(',', $result2[3])) * 2, 0);
            echo ">";
        }
        if ($result2[0] <> 'sms') {

            echo "$result2[2]<br>$result2[4]$result2[0]<br>$result2[1]";
            $strSec .= $result2[2] . ",";
            $strKb .= $result2[4] . $result2[0] . ",";
            $strDate .= $result2[1] . ",";
        } else {
            echo "Systems with Policy Auditor Agent";
        }
        if ($counts == 0) {
            echo "</td>";
        } else {
            echo "</td>";
        }
    }
    if ($total) {
        if ($counts == 0) {
            echo "<th>Fully patched<br>Systems</th>";
        } else {
            echo "<td colspan=$colspan>Fully patched<br>Systems</td>";
        }
    }
    echo "</tr>";
    echo "<tr id='header_id'>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    $query2 = mysql_query("select MSREF,MSBulletin,osserv from patchref where type='KB' and osserv not like '' order by SGBulletin desc ,MSBulletin desc", $db) or die("Cannot get patch list " . mysql_error());
    while ($result2 = mysql_fetch_row($query2)) {
        if ($result2[0] == 'sms') {
            $arrayos = explode(",", $result2[2]);
            for ($i = 0; $i < sizeof($arrayos); $i++) {
                $queryos = mysql_query("select distinct ad_os1 from fullreport where ad_os1 like '%$arrayos[$i]%'");
                $os = mysql_fetch_row($queryos);
                echo "<th>" . str_replace("Windows Server ", "", $arrayos[$i]) . "</th>";
            }
        }
        $arrayos = explode(",", $result2[2]);
        for ($i = 0; $i < sizeof($arrayos); $i++) {
            $queryos = mysql_query("select distinct ad_os1 from fullreport where ad_os1 like '%$arrayos[$i]%'");
            $os = mysql_fetch_row($queryos);
            if ($counts == 0) {
                echo "<th>" . str_replace("Windows Server ", "", $arrayos[$i]) . "</th>";
            } else {
                echo "<th colspan=2>" . str_replace("Windows Server ", "", $arrayos[$i]) . "</th>";
            }
        }
    }
    echo "</tr>";
//exit;
    if ($counts) {
        echo "<tr>";
        echo "<th>Count</th>";
        echo "<th>Count</th>";
        echo "<th>Count</th>";
        echo "<th>% AD</th>";
        echo "<th>Count</th>";
        echo "<th>% AD</th>";
    }
    $query2 = mysql_query("select MSREF,MSBulletin,SGBulletin,osserv from patchref where type='KB' and osserv not like '' order by SGBulletin desc,MSBulletin desc", $db) or die("Cannot get patch list " . mysql_error());
    $result2 = mysql_fetch_row($query2);
    $i = 1;
    while ($i < mysql_num_rows($query2)) {
        if ($counts) {
            $arrayos = explode(",", $result2[3]);
            for ($j = 0; $j < sizeof($arrayos) - 1; $j++) {
                echo "<th>Count</th>";
                echo "<th>% SMS</th>";
            }
        }
        $i++;
    }
    if ($total && $counts) {
        echo "<th>Count</th>";
        echo "<th>% SMS</th>";
    }
    if ($counts)
        echo "<tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($r = mysql_fetch_row($nbadinact)) {
        $class = 'Corner';
        switch ($type) {
            case 'deleg':
                $deleg = $r[1];
                if (substr($r[0], 0, strlen($sgtsprefix)) == $sgtsprefix) {
                    $delegname = substr($r[0], strlen($sgtsprefix), strlen($r[0]));
                    $class = 'SGTS';
                } else {
                    $delegname = $r[0];
                }
                $dc = '%';
                $ou = '%';
                $bu = '%';
                $site = '%';
                $sitelink = str_replace(" ", "*", $site);
                $bulink = str_replace("&", "$", str_replace(" ", "*", $bu));
                $oulink = str_replace("&", "$", str_replace(" ", "*", $ou));
                break;
            case 'ou':
                $ou = $r[0];
                $dc = $r[1];
                $bu = '%';
                $site = '%';
                $sitelink = str_replace("&", "$", str_replace(" ", "*", $site));
                $bulink = str_replace("&", "$", str_replace(" ", "*", $bu));
                $oulink = str_replace("&", "$", str_replace(" ", "*", $ou));
                break;
            case 'bu':
                $bu = $r[0];
                $site = '%';
                $bulink = str_replace("&", "$", str_replace(" ", "*", $bu));
                $oulink = str_replace("&", "$", str_replace(" ", "*", $ou));
                $sitelink = str_replace(" ", "*", $site);
                break;
            case 'site':
                $site = $r[0];
                $sitelink = str_replace(" ", "*", $site);
                $bulink = str_replace("&", "$", str_replace(" ", "*", $bu));
                $oulink = str_replace("&", "$", str_replace(" ", "*", $ou));
                break;
        }
        $strDel = str_replace(' ', "_", $delegname);
        if (($_GET['export'])) {
            echo "<tr><th><b>$r[0]</b></th>";
        } elseif ($type == 'deleg') {
            if ($deleg == 15) //exclusive for UTD
                echo "<tr><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&delegname=$strDel' target='_blank'><img src='img/excel.png'  title='export to a XLS file' width='23px' border='none' height='18px'></a></th><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&csv=csv&delegname=$strDel'  target='_blank'><img src='img/csv.png' alt='export to a CSV file' title='export to a CSV file' style='margin-left: 1px;height: 18px;width: 22.5px;margin-top:1.1px;border:none'></a></th><th class='SGTS' id=delgs  colspan='2'><p style='width:200px'><a href=pa_reportbydelegs4_server.php?deleg=$deleg>$delegname</a></p></th>";
            else
                echo "<tr><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&delegname=$strDel' target='_blank'><img src='img/excel.png'  title='export to a XLS file' width='23px' border='none' height='18px'></a></th><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&csv=csv&delegname=$strDel'  target='_blank'><img src='img/csv.png' alt='export to a CSV file' title='export to a CSV file' style='margin-left: 1px;height: 18px;width: 22.5px;margin-top:1.1px;border:none'></a></th><th class='SGTS' id=delgs  colspan='2'><p style='width:200px'><a href=" . $_SELF . "?deleg=$deleg style=''>$delegname</a></p></th>";
        } elseif ($type == 'site') {
            echo "<tr><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&ou=$oulink&dc=$dc&bu=$bulink&site=$site' target='_blank'><img src='img/excel.png' alt='export to an XLS file'  title='export to a XLS file' width='23px' border='none' height='18px'></a></th><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&csv=csv&ou=$oulink&dc=$dc&bu=$bulink&site=$site' target='_blank'><img src='img/csv.png' alt='export to a CSV file' title='export to a CSV file' style='margin-left: 1px;height: 18px;width: 22.5px;margin-top:1.1px;border:none'></a></th><th class='SGTS' id=delgs  colspan='2'><p style='width:200px'>$site</p></th>";
        } elseif ($type == 'ou') {
            echo "<tr><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&ou=$oulink&dc=$dc&bu=$bulink' target='_blank'><img src='img/excel.png' alt='export to an XLS file' title='export to a XLS file' width='23px' border='none' height='18px'></a></th><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&csv=csv&ou=$oulink&dc=$dc&bu=$bulink' target='_blank'><img src='img/csv.png' title='export to a CSV file' alt='export to a CSV file' style='margin-left: 1px;height: 18px;width: 22.5px;margin-top:1.1px;border:none'></a></th><th class='SGTS' id=delgs  colspan='2'><p style='width:200px'><a href=" . $_SELF . "?deleg=$deleg&ou=$oulink&dc=$dc&bu=$bulink>$dc\\$r[0]</a></p></th>";
        } else {
            echo "<tr><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&ou=$oulink&dc=$dc&bu=$bulink' target='_blank'><img src='img/excel.png' alt='export to an XLS file' title='export to a XLS file' width='23px' border='none' height='18px'></a></th><th colspan='1'><a href='exportpatches/export.php?deleg=$deleg&exps=exps&csv=csv&ou=$oulink&dc=$dc&bu=$bulink' target='_blank'><img src='img/csv.png' title='export to a CSV file' alt='export to a CSV file' style='margin-left: 1px;height: 18px;width: 22.5px;margin-top:1.1px;border:none'></a></th><th class='SGTS' id=delgs  colspan='2'><p style='width:200px'><a href=" . $_SELF . "?deleg=$deleg&ou=$oulink&dc=$dc&bu=$bulink>$r[0]</a></p></th>";
        }
        $versionmin = 'VIRUSCAN8600';
        $newversion = 'VIRUSCAN8800';
        $report = 'av';
        $query2 = mysql_query("select MSREF,MSBulletin,osserv from patchref where MSREF = 'sms' and osserv not like ''", $db) or die("Cannot get patch list " . mysql_error());
        $result2 = mysql_fetch_row($query2);
        $arrayos = explode(",", $result2[2]);
        for ($i = 0; $i < sizeof($arrayos); $i++) {
            $queryid = "ad_pc_" . $arrayos[$i];
            $$queryid = get_result();
            $total = "total_" . $queryid;
            $$total = ${$total} + ${$queryid}[0];
//            echo $queryid;exit;
            if (($type == 'deleg') || ($_GET['export'] == 1)) {
                if (${$queryid}[0] == 0) {
                    display_cell(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert);
                } else {
                    if ($queryid == 'ad_pc_Windows Server 2003') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2003%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2008') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2008%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2008 R2') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2008*R2%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2012 R2') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2012*R2%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2016') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2012*R2%&query=addetails_srv_ps");
                    }
                }
            } else {
                if (${$queryid}[0] == 0) {
                    display_cell(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert);
                } else {
                    if ($queryid == 'ad_pc_Windows Server 2003') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2003%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2008') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2008%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2008 R2') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2008*R2%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2012 R2') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2012*R2%&query=addetails_srv_ps");
                    } elseif ($queryid == 'ad_pc_Windows Server 2016') {
                        display_cell_withlink(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert, "av_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&os=%Windows*Server*2012*R2%&query=addetails_srv_ps");
                    }
//                display_cell(${$queryid}[0], 0, 0, 0, $ok, $middle, $alert);
                }
            }
        }

        $j = 0;
        $datevalid = date("Ymd", time() - (60 * 60 * 24 * 15));
        $query2 = mysql_query("select MSREF,MSBulletin,osserv from patchref where type='KB' and osserv not like '' order by SGBulletin desc,MSBulletin desc", $db) or die("Cannot get patch list " . mysql_error());
        while ($result2 = mysql_fetch_row($query2)) {
            $datepatch = substr($result2[1], 6, 4) . substr($result2[1], 3, 2) . substr($result2[1], 0, 2);
            mysql_query("set @date=$datepatch");
            $arrayos = explode(",", $result2[2]);
            for ($i = 0; $i < sizeof($arrayos); $i++) {
                $os = "%" . $arrayos[$i] . "%";
                $os = $arrayos[$i];
                $oslink = str_replace(" ", "*", $os);
                $queryid = 'patchs_srv';
                $patch = '%KB' . $result2[0] . '%';
                $result[$j] = get_result();
                $varname = $queryid . "_" . $os . "_" . $patch;
                $$varname = get_result();
                ${total_ . $varname} = ${total_ . $varname} + ${$varname}[0];
                $scope = "ad_pc_" . $arrayos[$i];
                if ($result2[0] == 'sms') {
                    ${sms . $arrayos[$i]} = ${$varname}[0];
                    $scope2 = ${$scope}[0];
                }
                if ($result2[0] != 'sms') {
                    $scope2 = ${sms . $arrayos[$i]};
                }
                $parameters = array();
                $parameters[0] = $deleg;
                $parameters[1] = $dc;
                $parameters[2] = $oulink;
                $parameters[3] = $bulink;
                $parameters[4] = $sitelink;
                $parameters[5] = $patch;
                $parameters[6] = $oslink;
                if (($type == 'deleg') || ($_GET['export'] == 1)) {
                    if ($scope2 > 0) {
                        if ($counts == 1) {
                            display_cell(number_format(${$varname}[0], 0, '.', ''), 0, 0, 0, $ok, $middle, $alert);
                        }
                        if ($patch == '%KBsms%') {
                            display_cell_export(number_format(${$varname}[0] / $scope2 * 100, 2, '.', ''), 1, 1, 0, $ok, $middle, $alert, $parameters); //aj
                        } else {
                            if ($datepatch < $datevalid) {
                                display_cell_export(number_format(${$varname}[0] / $scope2 * 100, 2, '.', ''), 1, 1, 0, $ok, $middle, $alert, $parameters); //aj
                            } else {
                                display_cell(number_format(${$varname}[0] / $scope2 * 100, 2, '.', ''), 0, 1, 0, $ok, $middle, $alert);
                            }
                        }
                    } else {
                        if ($counts == 1) {
                            echo "<th></th>";
                        }
                        echo "<th></th>";
                    }
                } else {
                    if ($scope2 > 0) {
                        if ($counts == 1) {
                            display_cell_withlink_patch(number_format(${$varname}[0], 0, '.', ''), 0, 0, 0, $ok, $middle, $alert, "pa_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&query=padetails_srv&patch=$patch&os=$oslink", $parameters);
                        }
                        if (${$varname}[0] / $scope2 * 100 == 100) {
                            display_cell(number_format(${$varname}[0] / $scope2 * 100, 2, '.', ''), 1, 1, 0, $ok, $middle, $alert, $parameters);
                        } else {
                            if ($patch == '%KBsms%') {
                                display_cell_withlink_patch(number_format(${$varname}[0] / $scope2 * 100, 2, '.', ''), 1, 1, 0, $ok, $middle, $alert, "pa_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&query=padetails_srv_sms&patch=$patch&os=$oslink", $parameters);
                            } else {
                                if ($datepatch < $datevalid) {
                                    display_cell_withlink_patch(number_format(${$varname}[0] / $scope2 * 100, 2, '.', ''), 1, 1, 0, $ok, $middle, $alert, "pa_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&query=padetails_srv&patch=$patch&os=$oslink", $parameters);
                                } else {
                                    display_cell_withlink_patch(number_format(${$varname}[0] / $scope2 * 100, 2, '.', ''), 0, 1, 0, $ok, $middle, $alert, "pa_detailed-new.php?deleg=$deleg&dc=$dc&ou=$oulink&bu=$bulink&site=$sitelink&type=$type&query=padetails_srv&patch=$patch&os=$oslink", $parameters);
                                }
                            }
                        }
                    } else {
                        if ($counts == 1) {
                            echo "<th></th>";
                        }
                        echo "<th></th>";
                    }
                }
                $j++;
            }
        }
        echo "</tr>";
    }
    ?>
    <!--<body ng-app="">
        <div  ng-init="first = '<?php echo 'adsada'; ?>'">
        </div>
        <div  ng-init="second = '<?php echo $a[1]; ?>'">
        </div>
    </body>-->
    <?php
    echo "</tbody><tfoot>";
    echo "<tr id='rowToClone'><th class='Corner' colspan='4'>Total</th>";
    $query2 = mysql_query("select MSREF,MSBulletin,osserv from patchref where MSREF = 'sms' and osserv not like '' ", $db) or die("Cannot get patch list " . mysql_error());
    $result2 = mysql_fetch_row($query2);
    $arrayos = explode(",", $result2[2]);
    for ($i = 0; $i < sizeof($arrayos) - 1; $i++) {
        $queryid = "ad_pc_" . $arrayos[$i];
        $$queryid = get_result();
        $total = "total_" . $queryid;
        display_cell(${$total}, 1, 0, 1, $ok, $middle, $alert);
    }
    $query2 = mysql_query("select MSREF,MSBulletin,osserv from patchref where type='KB'  and osserv not like '' order by SGBulletin desc,MSBulletin desc", $db) or die("Cannot get patch list " . mysql_error());
    while ($result2 = mysql_fetch_row($query2)) {
        $arrayos = explode(",", $result2[2]);
        for ($i = 0; $i < sizeof($arrayos) - 1; $i++) {
            $os = $arrayos[$i];
            $queryid = 'patchs_srv';
            $patch = '%KB' . $result2[0] . '%';
            $result[$j] = get_result();
            $varname = $queryid . "_" . $os . "_" . $patch;
            $scope = "total_ad_pc_" . $arrayos[$i];
            if ($result2[0] == 'sms') {
                ${sms . $arrayos[$i]} = ${total_ . $varname};
                $scope2 = ${$scope};
            }
            if ($result2[0] != 'sms') {
                $scope2 = ${sms . $arrayos[$i]};
            }
            if ($scope2 > 0) {
                display_cell(number_format(${total_ . $varname} / $scope2 * 100, 2), 1, 1, 1, $ok, $middle, $alert);
            } else {
                echo "<th></th>";
            }
        }
    }
    echo "</tr></tfoot>";
    echo "</table></center>";
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<SCRIPT type="text/javascript">
    d = document.getElementById("flushme");
    d.innerHTML = " ";

    var row = document.getElementById("rowToClone"); // find row to copy
    var table = document.getElementById("rowspan"); // find table to append to
    var clone = row.cloneNode(true); // copy children too
    clone.id = "newID"; // change id or other attributes/contents
    table.appendChild(clone); // add new row to end of table

    $(document).ready(function () {

        $('#header_id').after($('#newID'));
    });

    //$("ol").prepend("<li>Prepended item</li>");
</SCRIPT>
</body>
</html>		
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

