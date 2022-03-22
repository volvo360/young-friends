<?php

session_start();

error_reporting(E_ALL);

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

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
    $replaceTable = getReplaceTable(false);
	
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

	if (isset($_COOKIE["YF"]["user"]) && !isset($_SESSION['uid'])) 
	{
		$_POST['mail'] = $_COOKIE["YF"]["user"];
		$_POST['password'] = $_COOKIE["YF"]["pass"];

		$user = mysqli_real_escape_string($link, $_POST['mail']);
		$pass = mysqli_real_escape_string($link, $_POST['password']);

		include_once($path."ajaxLogin.php");
	}

	else if (isset($_COOKIE["YF_user"]) && !isset($_SESSION['uid'])) 
	{
		$_POST['mail'] = $_COOKIE["YF_user"];
		$_POST['password'] = $_COOKIE["YF_pass"];

		$user = mysqli_real_escape_string($link, $_POST['mail']);
		$pass = mysqli_real_escape_string($link, $_POST['password']);

		include_once($path."ajaxLogin.php");
	}

    if ($replaceTable[$_POST['replaceTable']] === PREFIX.'places')
    {
        showAjax_places();
    }
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'bangolfdate')
    {
		showAjax_bangolfdate_master();
    }
}

function showAjax_places()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_places = $langStrings['showAjax_places'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."places`";
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		if (((int)$row['lft'] + 1) === (int)$row['rgt'])
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_places[1]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<input type = text id =\"note[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."places"]."\" value = \"".$row['note']."\">";
				echo "</div>";
			echo "</div>";
        
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"p_adress[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_places[2]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<input type = text id =\"p_adress[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."places"]."\" value = \"".$row['p_adress']."\">";
				echo "</div>";
			echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"p_ort[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_places[3]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<input type = text id =\"p_ort[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."places"]."\" value = \"".$row['p_ort']."\">";
				echo "</div>";
			echo "</div>";
		}
		elseif (((int)$row['lft'] + 1) < (int)$row['rgt'])
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_places[4]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<input type = text id =\"note[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."places"]."\" value = \"".$row['note']."\">";
				echo "</div>";
			echo "</div>";
    	}
    }
}

function displayTournamentRestult()
{
	global $link;
	
	$table = "`".PREFIX."activities`";
	$table2 = "`".PREFIX."registered`";
	
	$table10 = "`".PREFIX."bangolfresultat`";
	$table11 = "`".PREFIX."user`";
	
	$table20 = "`".PREFIX."bangolfdate`";
	
	$sql = "SELECT *, YEAR(datestart) as year FROM (SELECT t1.datestart FROM ".$table20." t1 WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') as master";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$year = $row['year'];
	}
	
	$langStrings = getLangstrings();
    $displayTournamentRestult = $langStrings['displayTournamentRestult'];
	
	$tournamentHeader['player'] =  $displayTournamentRestult[1];
	$tournamentHeader['result'] =  $displayTournamentRestult[2];
	
	$sql = "SELECT * FROM (SELECT memberId, SUM(points) as result FROM ".$table10." WHERE year = '".$year."' GROUP BY memberId) as res INNER JOIN ".$table11." t11 ON t11.uid = res.memberId ORDER BY result DESC";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		echo "<table class = \"DataTable\">";
			echo "<thead>"; 
				echo "<tr>";
					foreach ($tournamentHeader as $key => $value)
					{
						echo "<th id = \"".$key."\">";
							echo $value;
						echo "</th>";
					}
				echo "</tr>";
			echo "</thead>";
		
			echo "<tbody>";
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					echo "<tr>";
						foreach ($tournamentHeader as $key => $value)
						{
							echo "<td>";
								if ($key == "player")
								{
									echo $row['firstName']." ".$row['sureName'];
								}
								else if ($key == "result")
								{
									echo $row['result'];
								}
							echo "</td>";
						}
					echo "</tr>";

				}
			echo "<tbody>";
		echo "</table>";
	}
}

function syncTournamentResult($ajax = false)
{
	global $link;
	
	$langStrings = getLangstrings();
    $syncTournamentResult = $langStrings['syncTournamentResult'];
	
	$table = "`".PREFIX."activities`";
	$table2 = "`".PREFIX."registered`";
	
	$table10 = "`".PREFIX."bangolfresultat`";
	$table11 = "`".PREFIX."user`";
	
	$sql = "SELECT * FROM (SELECT t1.aid, t2.uid, t11.firstName, t11.sureName, t11.tableKey as userTableKey FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t1.aid = t2.aid INNER JOIN ".$table11." t11 ON t11.uid = t2.uid  WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['selectActivity'])."') as master LEFT OUTER JOIN ".$table10." t10 ON master.uid = t10.memberId AND t10.aktivitet = master.aid  ORDER BY CASE WHEN resultat IS NULL THEN 4294967295 ELSE resultat END, firstName, sureName";
	//echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
	$first = true;
	
	echo "<form id = \"formTorunamentResuluts\">";
	
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if ($first)
		{
			$first = false;
			
			if (empty($row['holes']))
			{
				$holes = 18;
			}
			else
			{
				$holes = $row['holes'];
			}
			
			echo "<p>".$syncTournamentResult[1]." <input type = \"number\" min = \"9\" id = \"holes[".$_POST['selectActivity']."]\" value = \"".$holes."\" size = \"3\">"."</p>";
			
			echo "<table class = \"table table-stripe\" width = \"100%\">";
				echo "<thead>";
					echo "<tr>";
						echo "<th>";
							echo $syncTournamentResult[2];
						echo "</th>";
			
						echo "<th>";
							echo $syncTournamentResult[3];
						echo "</th>";
					echo "</tr>";
				echo "</thead>";
			
				echo "<tbody>";
		}
		
				echo "<tr>";
					echo "<td>";
						echo $row['firstName']." ".$row['sureName'];
					echo "</td>";
		
					echo "<td>";
						echo "<input type = \"number\" id = player[".$_POST['selectActivity']."_".$row['userTableKey']."] value = \"".$row['resultat']."\" size = \"4\">";
					echo "</td>";
				echo "</tr>";
		
				$blockMembers[$row['userTableKey']] = $row['userTableKey'];
		
				if (!empty($row['resultat']))
				{
					$update = true;
				}
	}
	
	
	
	if (!$first)
	{
			echo "</tbody>";
		echo "</table>";
		
		$replaceTable = getReplaceTable();
	
		echo "<input type = \"hidden\" id = \"replaceTable\" value = \"".$replaceTable[PREFIX.'bangolfresultat']."\">";

		echo "</form>";
		
		$members = getMembers();
		
		foreach ($blockMembers as $key => $value)
		{
			unset($members[$key]);
		}
		
		if (count($members) > 0 && !$ajax)
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"addMember2tournament\" class=\"col-sm-2 col-form-label\">".$syncTournamentResult[4]."</label>";

				echo "<div class=\"col-sm-8\">";

					echo "<select id = \"addMember2tournament[".$_POST['selectActivity']."]\" class = \"selectpicker\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" data-width = \"100%\">";
						foreach ($members as $key => $value)
						{
							echo "<option value = \"".$key."\"";
							echo ">".$value."</option>";
						}
					echo "</select>";
				echo "</div>";
			
				echo "<div class=\"col-sm-2\">";

					echo "<button id = \"\" class = \"btn btn-secondary form-control addMember2tournament\" data-replace_table = \"".$replaceTable[PREFIX."activities"]."\">";
						echo $syncTournamentResult[7];
					echo "</button";
				echo "</div>";
			echo "</div>";
			
		}
		echo "</div>";
		
		echo "<br>";
		if (!$update && !$ajax)
		{
			echo "<button class = \"btn btn-secondary updateTournament form-control\">".$syncTournamentResult[5]."</button>";
		}
		else if (!$ajax)
		{
			echo "<button class = \"btn btn-secondary updateTournament form-control\">".$syncTournamentResult[6]."</button>";
		}
		if (!$ajax)
		{
			echo "<br>";
			echo "<br>";
		}
	}
}

function regResultTournament()
{
	global $link;
	
	$langStrings = getLangstrings();
    $regResultTournament = $langStrings['regResultTournament'];
	
	$table = "`".PREFIX."bangolfdate`";
	$table2 = "`".PREFIX."bangolfresultat`";
	
	checkTable($table);
	checkTable($table2);
	
	$table10 = "`".PREFIX."activities`";
	$table11 = "`".PREFIX."user`";
   
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$startDate = $row['datestart'];
		$endDate = $row['datestop'];
		$ansvarig = $row['ansv'];
	}
	
	$sql = "SELECT * FROM ".$table10." WHERE datum >= '".$startDate."' AND datum <= CURDATE() AND (rep_activity_type <= 0 OR rep_activity_type IS NULL) AND datum <= '".$endDate."'  ORDER BY datum DESC LIMIT 1";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$preselect = $row['tableKey'];
	}
	
	$sql = "SELECT * FROM ".$table10." WHERE datum >= '".$startDate."' AND datum <= '".$endDate."' AND (rep_activity_type <= 0 OR rep_activity_type IS NULL) AND datum <= CURDATE() ORDER BY datum DESC";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
	if (mysqli_num_rows($result) > 0)
	{
		echo "<div class=\"row mb-3\">";
			echo "<label for=\"selectActivity\" class=\"col-sm-2 col-form-label\">".$regResultTournament[1]."</label>";
		
			echo "<div class=\"col-sm-10\">";
				echo "<select id = \"selectActivity\" class = \"selectpicker\" data-width = \"100%\" data-target_div = \"areaRegResult\">";
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						echo "<option value = \"".$row['tableKey']."\"";
							if ($preselect === $row['tableKey'])
							{
								echo "selected";
							}
						echo ">".fixutferror($row['rubrik'])."</option>";
					}
				echo "</select>";
			echo "</div>";
		echo "</div>";
		
		echo "<div id = \"areaRegResult\">";
			
			if (!empty($preselect))
			{
				$_POST['selectActivity'] = $preselect;
			
				syncTournamentResult();
			}
		
		echo "</div>";
	}
}

function showAjax_bangolfdate_master()
{
	global $link;
    
    $langStrings = getLangstrings();
    $showAjax_bangolfdate_master = $langStrings['showAjax_bangolfdate_master'];
	
	$tabsBangolfdate['displayTournamentRestult'] = $showAjax_bangolfdate_master[1];
	
	
	$table = "`".PREFIX."bangolfdate`";
	$table2 = "`".PREFIX."bangolfresultat`";
	
	$table10 = "`".PREFIX."activities`";
	$table11 = "`".PREFIX."user`";
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$year = date("Y", strtotime($row['datestart']));
		$ansvarig = $row['ansv'];
	}
	
	if ((int)date("Y") == (int)$year)
	{
		$activeRound = true;
	}
	else
	{
		$activeRound = false;
	}
	
	if (($_SESSION['uid'] == $ansvarig || isset($_SESSION['siteAdmin'])) && $activeRound)
	{
		$preeselect = "showAjax_bangolfdate";
		$tabsBangolfdate['showAjax_bangolfdate'] = $showAjax_bangolfdate_master[2];
	}
	else
	{
		$preeselect = key($tabsBangolfdate);
		$tabsBangolfdate['showAjax_bangolfdate'] = $showAjax_bangolfdate_master[3];
	}
  
	echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
		foreach ($tabsBangolfdate as $key => $value)
		{
			echo "<li class=\"nav-item\" role=\"presentation\">";
				echo "<button class=\"nav-link";
				if ($key == $preeselect)
				{
					echo " " ."active";
				}
				echo "\" id=\"".$key."-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#".$key."\" type=\"button\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
					if ($key == $preeselect)
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
		echo "<br>";
	
		foreach ($tabsBangolfdate as $key => $value)
		{
			echo "<div class=\"tab-pane fade";
				if ($preeselect == $key)
				{
					echo " "."show active";
				}
			echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
				call_user_func($key);
			echo "</div>";
		}
	echo "</div>";
}
	
function showAjax_bangolfdate()
{
	global $link;
	
	$replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."bangolfdate`";
	$table2 = "`".PREFIX."bangolfresultat`";
	
	$table10 = "`".PREFIX."activities`";
	$table11 = "`".PREFIX."user`";
	$table12 = "`".PREFIX."registered`";
	
	$langStrings = getLangstrings();
    $showAjax_bangolfdate = $langStrings['showAjax_bangolfdate'];
    
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$year = date("Y", strtotime($row['datestart']));
		$ansvarig = $row['ansv'];
		
		$endDate = date("Y-m-d", strtotime($row['datestop'] ."+ 14 day"));
	}
	
	$sql = "SELECT *, activities.tableKey FROM (SELECT * FROM ".$table2." t2 WHERE year = '".$year."' LIMIT 4294967295) as activities INNER JOIN ".$table10." t10 ON t10.aid = activities.aktivitet  INNER JOIN ".$table11." t11 ON t11.uid = activities.memberId ORDER BY datum, resultat, firstName, sureName";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
	if (mysqli_num_rows($result) == 0)
	{
		if ($_SESSION['uid'] !== $ansvarig && !isset($_SESSION['siteAdmin']))
		{
			echo "<h4>".$showAjax_bangolfdate[8]."</h4>";
			
			return false;
		}
		else
		{
			regResultTournament();
			return false;
		}
	}
	
	$first = true;
	
	$old = null;
	
	if (($_SESSION['uid'] == $ansvarig || isset($_SESSION['siteAdmin'])) && $endDate >= date("Y-m-d"))
	{
		regResultTournament();
		return false;
	}
	
	echo "<div class=\"accordion\" id=\"accordionTournament\">";
		echo "<div class=\"accordion-item\">";
			echo "<h2 class=\"accordion-header\" id=\"headingSummary\">";
				echo "<button class=\"accordion-button\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapseSummary\" aria-expanded=\"true\" aria-controls=\"collapseSummary\">";
					echo $showAjax_bangolfdate[7];;
				echo "</button>";
			echo "</h2>";
			echo "<div id=\"collapseSummary\" class=\"accordion-collapse collapse show\" aria-labelledby=\"headingSummary\" data-bs-parent=\"#accordionTournament\">";
				echo "<div class=\"accordion-body\">";
					$sql = "SELECT SUM(points) as count, activities.tableKey, firstName, sureName FROM (SELECT * FROM ".$table2." t10 WHERE year = '".$year."' LIMIT 4294967295) as activities INNER JOIN ".$table10." t10 ON t10.aid = activities.aktivitet INNER JOIN ".$table11." t11 ON t11.uid = activities.memberId GROUP BY memberId ORDER BY count DESC, firstName, sureName";
					$result2= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
					
					echo "<table width = \"100%\">";
							echo "<thead>";
				
								echo "<tr>";

									echo "<th>";
										echo $showAjax_bangolfdate[2];
									echo "</th>";

									echo "<th>";
										echo $showAjax_bangolfdate[6];
									echo "</th>";

								echo "</tr>";
				
							echo "</thead>";
	
							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								echo "<tr>";
									echo "<td>".$row2['firstName']." ".$row2['sureName']."</td>";
								
									echo "<td>".$row2['count']."</td>";
								echo "</tr>";
							}

						echo "</table>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($old !== $row['rubrik'])
			{
				if ($first)
				{
					$first = false;
				}
				else
				{	
								echo "</table>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
				}

				echo "<div class=\"accordion-item\">";
					echo "<h2 class=\"accordion-header\" id=\"heading_".$row['tableKey']."\">";
						echo "<button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapse_".$row['tableKey']."\" aria-expanded=\"false\" aria-controls=\"collapse_".$row['tableKey']."\">";
							echo $row['datum']." ".fixutferror($row['rubrik']);
						echo "</button>";
					echo "</h2>";

					echo "<div id=\"collapse_".$row['tableKey']."\" class=\"accordion-collapse collapse\" aria-labelledby=\"heading_".$row['tableKey']."\" data-bs-parent=\"#accordionTournament\">";
						echo "<div class=\"accordion-body\">";

							echo "<p>".$showAjax_bangolfdate[1]." ".$row['holes']."</p>";
							echo "<table width = \"100%\">";
							$old = $row['rubrik'];
				
							echo "<thead>";
				
								echo "<tr>";

									echo "<th>";
										echo $showAjax_bangolfdate[2];
									echo "</th>";

									echo "<th>";
										echo $showAjax_bangolfdate[3];
									echo "</th>";

									echo "<th>";
										echo $showAjax_bangolfdate[4];
									echo "</th>";

									echo "<th>";
										echo $showAjax_bangolfdate[5];
									echo "</th>";

								echo "</tr>";
				
							echo "</thead>";
			}

							echo "<tr>";
								echo "<td>".$row['firstName']." ".$row['sureName']."</td>";

								echo "<td>".$row['resultat']."</td>";

								echo "<td>".round((int)$row['resultat']/(int)$row['holes'],2)."</td>";

								echo "<td>".$row['points']."</td>";
							echo "</tr>";
		}
		
		if (!$first)
		{
					echo "</table>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		}
	
	echo "</div>";
}
	

?>