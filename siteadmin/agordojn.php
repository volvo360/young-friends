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

function printAgordojn()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printAgordojn = $langStrings['printAgordojn'];
    
    //updateTranslationTable();
    
    $table = "`".PREFIX."settings_site`";
    
    checkTable($table);
	
	$replaceTable = getReplaceTable();
    
    $sql = "SELECT node.* FROM ".$table." AS node GROUP BY node.groupName ORDER BY node.groupName;";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    $tree['bank'] = $printAgordojn[1];
    $tree['sms'] = $printAgordojn[2];
    $tree['mail'] = $printAgordojn[3];
    $tree['mail_sms_generally'] = $printAgordojn[4];
	$tree['generally'] = $printAgordojn[6];
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 60vh; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."settings_site"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."settings_site"]."\" data-replace_table = \"".$replaceTable[PREFIX.'settings_site']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."settings_site"]."-data\" style = \"display:none;\" >";
					echo "<li id = \"".$replaceTable[PREFIX."block_date"]."\" class = \"folder\">".$printAgordojn[5]."</li>";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

                        if ($oldDepth > (int)(int)$row['depth'])
                        {
                            for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }

                        if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                        {
                            echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$tree[$row['groupName']]."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\" class = \"folder\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$tree[$row['groupName']]."</li>";
                        }

                        $oldDepth = (int)$row['depth'];
                    }

                    if ($oldDepth > 0)
                    {
                        for ($i = 0; $i < ($oldDepth); $i++)
                        {
                            echo "</ul></li>";
                        }
                    }
                echo "</ul>";
            echo "</div><br>";

            /*echo "<form id = \"addForm_".$replaceTable[PREFIX."settings_site"]."\">";
                echo $printVarTree[1]."<br>";
                echo "<input type = \"text\" id = \"variable\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."settings_site"]."\" data-replace_table = \"".$replaceTable[PREFIX."settings_site"]."\">".$printVarTree[2]."</button>";
            echo "</form><br><br>";*/

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 60vh; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."settings_site"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

    echo "</div>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $agordojn = $langStrings['agordojn'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$agordojn[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 60vh; height : 60vh;\">";
                echo "<p>";
                    printAgordojn();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>