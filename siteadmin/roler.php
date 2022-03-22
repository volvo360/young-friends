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



function printRoler()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printRoler = $langStrings['printRoler'];
    
    $table = "`".PREFIX."roles`";
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT node.*, (COUNT(parent.lft) - 1) AS depth
                FROM ".$table." AS node,
                        ".$table." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft;";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."roles"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."roles"]."\" data-replace_table = \"".$replaceTable[PREFIX.'roles']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."roles"]."-data\" style = \"display:none;\" >";
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

                            echo ">".$row['assignment_name']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['assignment_name']."</li>";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."roles"]."\">";
                echo $printRoler[1]."<br>";
                echo "<input type = \"text\" id = \"assignment_name\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."roles"]."\" data-replace_table = \"".$replaceTable[PREFIX."roles"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$printRoler[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."roles"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

    echo "</div>";
    
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $roler = $langStrings['roler'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$roler[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 60vh; height : 60vh;\">";
                echo "<p>";
                    printRoler();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>