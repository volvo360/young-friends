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
include_once($path."ajax_editActivity.php");

function render_activity()
{
	global $link;
	
	$langStrings = getLangstrings();
    $editActivity = $langStrings['editActivity'];
	
	echo "<div class=\"row mb-3\">";
		echo "<label for=\"editActivity\" class=\"col-sm-2 col-form-label\">".$editActivity[2]."</label>";
		echo "<div class=\"col-sm-10\">";
			echo "<select class = \"selectpicker show-tick\" id = \"editActivity\" data-size = \"5\" data-width = \"100%\" data-target_div = \"editActivityArea\">";
				echo "<option value = \"-1\">".$editActivity[3]."</option>";
				
				$table = "`".PREFIX."activities`";
	
				$sql = "SELECT * FROM ".$table." WHERE datum >= curdate() ORDER BY datum DESC, tid DESC";
				$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					echo "<option value = \"".$row['tableKey']."\">".$row['rubrik']."</option>";	
				}
			echo "</select>";
		echo "</div>";
	echo "</div>";
	
	echo "<div id = \"editActivityArea\">";
	
		renderActityBody();
	
	echo "</div>";
}

function render_places()
{
	global $link;
    
    $langStrings = getLangstrings();
    $render_places = $langStrings['render_places'];
    
    $table = "`".PREFIX."places`";
    
    checkTable($table);
    
    $replaceTable = getReplaceTable();
    
    $sql = "SELECT node.*, (COUNT(parent.lft) - 1) AS depth
                FROM ".$table." AS node,
                        ".$table." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft;";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."places"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."places"]."\" data-replace_table = \"".$replaceTable[PREFIX.'places']."\" style = \"max-height : 37vh; height:37vh; overflow:auto;\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."places"]."-data\" style = \"display:none;\" >";
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

                            echo ">".$row['note']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['note']."</li>";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."places"]."\">";
                echo $render_places[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."places"]."\" data-replace_table = \"".$replaceTable[PREFIX."places"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$render_places[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 40vh; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."places"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

    echo "</div>";
}

function editActivity()
{
	$langStrings = getLangstrings();
    $editActivity = $langStrings['editActivity'];
	
	$navs['activity'] = $editActivity[4];
	$navs['places'] = $editActivity[5];
	
	$preselect = "activity";
	
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
				echo "\">".$value."</button>";
			echo "</li>";
		}
	echo "</ul>";
		
	echo "<div class=\"tab-content\" id=\"myTabContent\">";
		foreach ($navs as $key => $value)
		{
			echo "<div class=\"tab-pane fade";
				if ($key == $preselect)
				{
					echo " "."show active";
				}
			echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\" >";
				echo "<br>";
				call_user_func("render_".$key);
			echo "</div>";
		}
		
	echo "</div>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $editActivity = $langStrings['editActivity'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$editActivity[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height :61vh; overflow:auto;\">";
                echo "<p>";
                    editActivity();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>