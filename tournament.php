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
include_once($path."showAjax.php");

function printTournament()
{
    global $link;
    
    $langStrings = getLangstrings();
    $printTournament = $langStrings['printTournament'];
    
    $table = "`".PREFIX."bangolfdate`";
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT * FROM ".$table." ORDER BY datestart DESC";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 59vh; height : 59vh; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."bangolfdate"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."bangolfdate"]."\" data-replace_table = \"".$replaceTable[PREFIX.'bangolfdate']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."bangolfdate"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

						echo "<li id = \"".$row['tableKey']."\"";

							if (array_key_exists($row['tableKey'], $display))
							{
								echo " "."data-selected = true";
							}

						echo ">".$row['datestart']."-".$row['datestop']."</li>";
                        
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
		echo "</div>";
            
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."bangolfdate"]."\">";
				$_POST['id'] = reset($rowData)['tableKey'];
               	showAjax_bangolfdate_master();
            echo "</div>";
        echo "</div>";

    echo "</div>";   
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $tournament = $langStrings['tournament'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$tournament[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height :61vh; overflow:auto;\">";
                echo "<p>";
                    printTournament();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>