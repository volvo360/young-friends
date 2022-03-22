<?php

session_start();

echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";
echo "<meta name=\"robots\" content=\"noindex\" />";

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
include_once($path."common/modal.php");
include_once($path."estraro/showAjax.php");

function showAccounting()
{
	global $link;
	
	$replaceTable = getReplaceTable();
	
	$langStrings = getLangstrings();
    $showTagrodo = $langStrings['showTagrodo'];
	
	$table = "`".PREFIX."accounting_year`";
	
	checkTable($table);
	
	$sql = "SELECT node.* FROM ".$table." as node ORDER BY node.year DESC;";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
	$oldYear = null;
	$first = true;
	
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-2\" style = \"max-height : 57vh; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."accounting_year"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."accounting_year"]."\" data-replace_table = \"".$replaceTable[PREFIX.'accounting_year']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."accounting_year"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
						$rowData[] = $row;
					    echo "<li id = \"".$row['tableKey']."\">".$row['year']."</li>";
                    }

                 echo "</ul>";
            echo "</div><br>";

            /*echo "<form id = \"addForm_".$replaceTable[PREFIX."border_agenda"]."\">";
                echo $showTagrodo[1]."<br><br>";
                echo "<input type = \"date\" min = \"".date("Y-m-d")."\"id = \"meetingDay\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."border_agenda"]."\" data-replace_table = \"".$replaceTable[PREFIX."border_agenda"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$showTagrodo[2]."</button>";
            echo "</form><br><br>";*/

        echo "</div>";
        echo "<div class = \"col-md-10\" style = \"max-height : 57vh; height : 57vh; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."accounting_year"]."\">";
	
				$id = reset($rowData)['tableKey'];
				$_POST['id'] = $id;
	
               showAjax_accounting_year();
            echo "</div>";
        echo "</div>";

    echo "</div>";
	
	
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $accounting = $langStrings['accounting'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$accounting[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 61vh; height : 61vh; overflow : auto;\">";
                echo "<p>";
                    showAccounting();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
	print_modal();
	printFooter();
	echo "<script src=\"".$url."estraro/estraro.js\"></script>";
}

?>