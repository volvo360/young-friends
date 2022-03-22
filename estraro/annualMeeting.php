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

function annualMeeting()
{
	global $link;
	
	$replaceTable = getReplaceTable();
	
	$langStrings = getLangstrings();
    $annualMeeting = $langStrings['annualMeeting'];
	
	$table = "`".PREFIX."annualMeeting_agenda`";
	
	$sql = "SELECT node.* FROM ".$table." AS node ORDER BY node.meetingDay DESC;";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
	$oldYear = null;
	$first = true;
	
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 60vh; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."annualMeeting_agenda"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."annualMeeting_agenda"]."\" data-replace_table = \"".$replaceTable[PREFIX.'annualMeeting_agenda']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."annualMeeting_agenda"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
						if ($oldYear !== date("Y", strtotime($row['meetingDay'])))
						{
							echo "<li class = \"folder";
								if ($first)
								{
									$_POST['id'] = $row['tableKey'];
									
									$first = false;
									echo " "."expanded";
								}
							echo "\" id = \"".date("Y")."\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".date("Y", strtotime($row['meetingDay']))."<ul>";
							
							$oldYear = date("Y", strtotime($row['meetingDay']));
						}
						
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

                            echo ">".$row['meetingDay']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], $display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['meetingDay']."</li>";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."annualMeeting_agenda"]."\">";
                echo $annualMeeting[1]."<br><br>";
                echo "<input type = \"date\" min = \"".date("Y-m-d")."\" value = \"".date("Y-m-d", strtotime("first saturday of February"))."\" id = \"meetingDay\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."annualMeeting_agenda"]."\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$annualMeeting[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" >";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."annualMeeting_agenda"]."\">";
               showAjax_annualMeeting_agenda();
            echo "</div>";
        echo "</div>";

    echo "</div>";
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $annualMeeting = $langStrings['annualMeeting'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$annualMeeting[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 61vh; height : 61vh; overflow : auto;\">";
                echo "<p>";
                    annualMeeting();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
	print_modal();
    printFooter();
	echo "<script src=\"".$url."estraro/estraro.js\"></script>";
}

?>