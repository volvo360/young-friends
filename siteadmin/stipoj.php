<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";

if ($_SERVER['SERVER_NAME'] === "server01")
    {
        $url = "//server01/flexshare/yf/";
        $path = "/var/flexshare/shares/yf/";
    }
    else if ($_SERVER['SERVER_NAME'] === "localhost")
    {
        $url = "//localhost/yf/";
        $path = $_SERVER['DOCUMENT_ROOT']."/yf/";
    }
    else
    {
        $url = "//www.young-friends.org/";
        $path = $_SERVER['DOCUMENT_ROOT']."/";
    }

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/theme.php");

function printSMSLog()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printSMSLog = $langStrings['printSMSLog'];
    
    $table = "`".PREFIX."log`";
    $table2 = "`".PREFIX."user`";
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT node.*, t2.* FROM ".$table." AS node INNER JOIN ".$table2." t2 ON node.user = t2.uid WHERE sms > 0  ORDER BY CASE WHEN sent IS NULL THEN 18446744073709551615 ELSE sent END DESC LIMIT 50;";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    if (mysqli_num_rows($result) > 0)
    {
        echo "<table id = \"tablePrintSMSLog\" class=\"DataTable table table-striped\" style = \"max-height : 60vh; height : 60vh; over-flow:auto; width:100%;\">";    
        
        echo "<thead>";
            echo "<tr>";
                echo "<th>";
                    echo $printSMSLog[1];
                echo "</th>";
        
                echo "<th id = \"sentDate\">";
                    echo $printSMSLog[2];
                echo "</th>";
        
                echo "<th >";
                    echo $printSMSLog[3];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[4];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[5];
                echo "</th>";
            echo "</tr>";
        echo "</thead>";
        
        echo "<tbody >";
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<tr>";
                echo "<td>";
                    echo $row['to'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['sent'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['header'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['rawMessage'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['firstName']." ".$row['sureName'];
                echo "</td>";
            echo "</tr>";
        } 
        echo "<tbody>";
        
        echo "<tfoot>";
            echo "<tr>";
                echo "<th>";
                    echo $printSMSLog[1];
                echo "</th>";
        
                echo "<th id = \"sentDate\">";
                    echo $printSMSLog[2];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[3];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[4];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[5];
                echo "</th>";
            echo "</tr>";
        echo "</tfoot>";
        
        echo "</table>";
    }
}

function printMailLog()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printSMSLog = $langStrings['printSMSLog'];
    
    $table = "`".PREFIX."log`";
    $table2 = "`".PREFIX."user`";
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT node.*, t2.* FROM ".$table." AS node INNER JOIN ".$table2." t2 ON node.user = t2.uid WHERE node.email > 0  ORDER BY CASE WHEN sent IS NULL THEN 18446744073709551615 ELSE sent END DESC LIMIT 50;";
    
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    if (mysqli_num_rows($result) > 0)
    {
        echo "<table id = \"tablePrintMailLog\" class=\"DataTable table table-striped\" style = \"max-height : 60vh; height : 60vh; over-flow:auto; width:100%;\">";    
        
        echo "<thead>";
            echo "<tr>";
                echo "<th>";
                    echo $printSMSLog[1];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[2];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[3];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[4];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[5];
                echo "</th>";
            echo "</tr>";
        echo "</thead>";
        
        echo "<tbody>";
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<tr>";
                echo "<td>";
                    echo $row['to'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['sent'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['header'];
                echo "</td>";
            
                echo "<td>";
                    echo nl2br($row['rawMessage']);
                echo "</td>";
            
                echo "<td>";
                    echo $row['firstName']." ".$row['sureName'];
                echo "</td>";
            echo "</tr>";
        } 
        echo "<tbody>";
        
        echo "<tfoot>";
            echo "<tr>";
                echo "<th>";
                    echo $printSMSLog[1];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[2];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[3];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[4];
                echo "</th>";
        
                echo "<th>";
                    echo $printSMSLog[5];
                echo "</th>";
            echo "</tr>";
        echo "</tfoot>";
        
        echo "</table>";
    }
}

function printCronLog()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printCronLog = $langStrings['printCronLog'];
    
    $table = "`".PREFIX."cron_log`";
    
    //checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT node.* FROM ".$table." AS node ORDER BY CASE WHEN log_date IS NULL THEN 18446744073709551615 ELSE log_date END DESC LIMIT 500;";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    if (mysqli_num_rows($result) > 0)
    {
        echo "<table id = \"tablePrintCronlLog\" class=\"DataTable table table-striped\" style = \"max-height : 60vh; height : 60vh; over-flow:auto; width:100%;\" >";    
        
        echo "<thead>";
            echo "<tr>";
                echo "<th>";
                    echo $printCronLog[1];
                echo "</th>";
        
                echo "<th>";
                    echo $printCronLog[2];
                echo "</th>";
            echo "</tr>";
        echo "</thead>";
        
        echo "<tbody>";
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<tr>";
                echo "<td>";
                    echo $row['log_date'];
                echo "</td>";
            
                echo "<td>";
                    echo $row['event'];
                echo "</td>";
            echo "</tr>";
        } 
        echo "<tbody>";
        
        echo "<tfoot>";
            echo "<tr>";
                echo "<th>";
                    echo $printCronLog[1];
                echo "</th>";
        
                echo "<th>";
                    echo $printCronLog[2];
                echo "</th>";
            echo "</tr>";
        echo "</tfoot>";
        
        echo "</table>";
    }
}


function printLog()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printLog = $langStrings['printLog'];
    
    $navs['printSMSLog'] = $printLog[1];
    $navs['printMailLog'] = $printLog[2];
    $navs['printCronLog'] = $printLog[3];
    
    $preselect = 'printSMSLog';
    
    echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
        foreach ($navs as $key => $value)
        {
            echo "<li class=\"nav-item\" role=\"presentation\">";
                echo "<button class=\"nav-link";
                    if ($key == $preselect)
                    {
                        echo " "."active";
                    }
                echo "\" id=\"".$key."-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#".$key."\" type=\"button\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                    if ($key == $preselect)
                    {
                        echo "true";
                    }
                    else
                    {
                        echo "false";
                    }
                echo"\">".$value."</button>";
            echo "</li>";
        }
    echo "</ul>";
    
    echo "<div class=\"tab-content\" id=\"myTabContent\" style = \"max-height : 60vh; height : 60vh; over-flow:auto; width:100%;\">";
        foreach ($navs as $key => $value)
        {
            echo "<div class=\"tab-pane fade";
                if ($key == $preselect)
                {
                    echo " "."show active";
                }
            echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\" style = \"max-height : 60vh; height : 60vh; over-flow:auto;\" >";
                echo "<br>";
                call_user_func($key);
            echo "</div>";
        }
        
    echo "</div>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $stipoj = $langStrings['stipoj'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$stipoj[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 60vh; height : 60vh; over-flow:auto;\" >";
                echo "<p>";
                    printLog();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>