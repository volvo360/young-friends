<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";

if ($_SERVER['SERVER_NAME'] === "server01")
{
    $url = "//server01/flexshare/yf/";
    $path = "/var/flexshare/shares/yf/";
}

elseif ($_SERVER['SERVER_NAME'] === "localhost")
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

function printStatutes()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printStatutes = $langStrings['printStatutes'];
    
    $table = "`".PREFIX."statutes`";
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT * FROM ".$table." WHERE active > 0 ORDER BY validFrom DESC LIMIT 1";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        echo $row['statutes'];
    }    
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $statues = $langStrings['statues'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$statues[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height :61vh; overflow:auto;\">";
                echo "<p>";
                    printStatutes();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>