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

function showCadastre()
{
	global $link;
	
	$langStrings = getLangstrings();
    $showCadastre = $langStrings['showCadastre'];
	
	$table = "`".PREFIX."user`";
	
	$table10 = "`".PREFIX."groups`";
	$table11 = "`".PREFIX."roles`";
	$table12 = "`".PREFIX."mission`";
    
    $sql = "SELECT * FROM ".$table." WHERE betalt > CURDATE() ORDER BY firstName, sureName";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result))
    {
        $membersData[$row['autoId']] = $row;
	}
	
	$sql = "SELECT * FROM ".$table12." t12 RIGHT OUTER JOIN (SELECT node.*, (COUNT(parent.lft) - 1) AS depth
				FROM ".$table10." AS node,
						".$table10." AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.visible > 0
				GROUP BY node.lft
				ORDER BY node.lft) as groups RIGHT OUTER JOIN ".$table11 ." t11 ON t11.groupId = groups.groupId  ON t12.assignment_id = t11.assignment_id WHERE t11.visible > 0 ORDER BY CASE WHEN groups.lft IS NULL THEN 4294967295 ELSE groups.lft END, t11.lft";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result))
    {
		if (!empty($row['uid']))
		{
			$membersData[$row['uid']]['rolles'][] = $row['assignment_name'];
		}
	}
	
	$th['firstName'] = $showCadastre[1];
	$th['sureName'] = $showCadastre[2];
	$th['address'] = $showCadastre[3];
	$th['city'] = $showCadastre[4];
	$th['phone'] = $showCadastre[5];
	$th['mobile'] = $showCadastre[6];
	$th['email'] = $showCadastre[7];
	
	$th['rolles'] = $showCadastre[8];
	
	echo "<table class = \"table table-responsive DataTable\" witdht = \"100%\">";
		echo "<thead>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</thead>";
	
		echo "<tbody>";
			foreach ($membersData as $key => $value)
			{
				echo "<tr>";
					foreach ($th as $key2 => $value2)
					{
						if ($key2 == "rolles")
						{
							echo "<td id = \"".$key2."\">".implode(", ", $value[$key2])."</td>";
						}
						else
						{
							echo "<td id = \"".$key2."\">".$value[$key2]."</td>";
						}
					}
				echo "</tr>";
			}
		echo "</tbody>";
	
		echo "<tfoot>";
			echo "<tr>";
				foreach ($th as $key => $value)
				{
					echo "<th id = \"".$key."\">".$value."</th>";
				}
			echo "</tr>";
		echo "</tfoot>";
	echo "</table>";
	
	if (isset($_SESSION['border']))
	{
		echo "<br><br><p>";
			echo "<a href=\"".$url."estraro/adminCadastre.php\" class = \"btn btn-secondary form-control\" >".$showCadastre[9]."</a>";
		echo "</p>";
	}
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
    printHeader();
    
    $langStrings = getLangstrings();
    $cadastre = $langStrings['cadastre'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$cadastre[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" style = \"max-height : 61vh; height : 61vh; overflow : auto;\">";
                echo "<p>";
                    showCadastre();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    
    printFooter();
}

?>