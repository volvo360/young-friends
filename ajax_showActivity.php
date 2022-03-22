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

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");

$table = "`".PREFIX."activities`";
$table1 = "`".PREFIX."registered`";

$table10 = "`".PREFIX."user`";

checkTable($table1);

$sql = "SELECT *, t1.tableKey as userTableKey FROM ".$table." t LEFT OUTER JOIN ".$table1." t1 ON t.aid = t1.aid LEFT OUTER JOIN ".$table10." t10 ON t10.uid = t1.uid WHERE t.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' ORDER BY firstName, sureName";
echo __LINE__." ".$sql."<br>";
$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$rowData[] = $row;
}

$langStrings = getLangstrings();
$ajax_showActivity = $langStrings['ajax_showActivity'];

$replaceTable = getReplaceTable();

echo "<div id = \"modalHeader\">";
	echo reset($rowData)['rubrik'];
echo "</div>";

echo "<div id = \"modalBody\">";
	$first = true;

	foreach ($rowData as $key => $value)
	{
		if ($first)
		{
			$first = false;
			
			if ((int)$value['rep_activity_type'] == 2)
			{
				echo "<h4 style = \"color : red;\">";
					echo $ajax_showActivity[9];
				echo "</h4>";
			}
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"aktivitet\" class=\"col-sm-2 col-form-label\">".$ajax_showActivity[1]."</label>";
					
				echo "<div class=\"col-sm-10\" id = \"aktivitet\">";
					echo nl2br($value['aktivitet']);
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"date\" class=\"col-sm-2 col-form-label\">".$ajax_showActivity[2]."</label>";
					
				echo "<div class=\"col-sm-10\" id = \date\">";
					echo $value['datum']." ".$value['tid'];
				echo "</div>";
			echo "</div>";
			
			if (isset($_SESSION['uid']))
			{			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"ansvarig\" class=\"col-sm-2 col-form-label\">".$ajax_showActivity[20]."</label>";

					echo "<div class=\"col-sm-10\" id = \"ansvarig\">";
						$sql = "SELECT * FROM ".$table10." WHERE uid = '".$value['ansvarig']."'";
							$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

							while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
							{
								echo $row['firstName']." ".$row['sureName'];
							}
					echo "</div>";
				echo "</div>";
			}
			
			if (!empty($value['pris']))
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"price\" class=\"col-sm-2 col-form-label\">".$ajax_showActivity[3]."</label>";

					echo "<div class=\"col-sm-10\" id = \"price\">";
						echo $value['pris']." ".$ajax_showActivity[4];
						if (!empty($value['pree_pay']))
						{
							echo " ".$ajax_showActivity[5];
						}
					echo "</div>";
				echo "</div>";
			}
			
			if (!empty($value['firstName']) && isset($_SESSION['uid']))
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"count\" class=\"col-sm-2 col-form-label\">".$ajax_showActivity[6]."</label>";

					echo "<div class=\"col-sm-10\" id =  \"member[".$value['tableKey']."]\">";
						echo count($rowData)." ".$ajax_showActivity[7];
					echo "</div>";
				echo "</div>";
				
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"member\" class=\"col-sm-2 col-form-label\">".$ajax_showActivity[10]."</label>";

					echo "<div class=\"col-sm-10\" id = \"member[".$value['tableKey']."]\">";
				
						if (!empty($value['outsiders']))
						{
							echo $value['outsiders']. " via ";
						}
				
						echo $value['firstName']." ".$value['sureName']." ";
				
						if ((int)$value['banml'] == 0 && date("Y-m-d") <= $value['datum'])
						{
							echo "<button id = \"aid[".$value['userTableKey']."]\" class = \"btn btn-secondary unsubscribeActivity\">".$ajax_showActivity[11]."</button>";
						}
					echo "</div>";
				echo "</div>";
			}
			else
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"inputEmail3\" class=\"col-sm-2 col-form-label\">".$ajax_showActivity[6]."</label>";

					echo "<div class=\"col-sm-10\">";
						if (count($rowData) > 0)
						{
							if (count($rowData) == 1)
							{
								if (empty($value['userTableKey']))
								{
									echo $ajax_showActivity[8];
								}
								else
								{
									echo count($rowData)." person";
								}
							}
							else
							{
								echo count($rowData)." personer";
							}
						}
						else
						{
							echo $ajax_showActivity[8];
						}
					echo "</div>";
				echo "</div>";
			}
		}
		else if (isset($_SESSION['uid']))
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"unsubscribeActivity[".$value['userTableKey']."\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

				echo "<div class=\"col-sm-10\">";
			
					if (!empty($value['outsiders']))
					{
						echo $value['outsiders']. " via ";
					}
			
					echo $value['firstName']." ".$value['sureName']." ";
					if ((int)$value['banml'] == 0 && date("Y-m-d") <= $value['datum'])
					{
						echo "<button id = \"unsubscribeActivity[".$value['userTableKey']."]\" class = \"btn btn-secondary unsubscribeActivity\">".$ajax_showActivity[11]."</button>";
					}
				echo "</div>";
			echo "</div>";
		}
	}

	if (isset($_SESSION['uid']))
	{
		echo "<form id = \"activityForm\">";
		
			if ((int)reset($rowData)['banml'] > 0)
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"banml\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

					echo "<div class=\"col-sm-10\" id = \"banml\">";
						echo "<h4 style = \"color : red;\">";
							echo $ajax_showActivity[12];
						echo "</h4>";
					echo "</div>";
				echo "</div>";
			}
        
			if (((int)reset($rowData)['maxantal'] < count($rowData) || empty(reset($rowData)['maxantal'])) && date("Y-m-d") <= $value['datum'])
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"addMember2activity\" class=\"col-sm-2 col-form-label\">"."&nbsp;";
				echo "</label>";

					echo "<div class=\"col-sm-10\" id = \"addMember2activity\">";
						echo $ajax_showActivity[13]. "&nbsp;";

						$sql = "SELECT * FROM ".$table10." WHERE uid = '".$_SESSION['uid']."'";
						$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

						while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
							$userTableKey = $row['tableKey'];
						}
				
						$members = getMembers();

						echo "<select class = \"selectpicker show-tick\" data-live-search = \"true\" id = \"addMember2activity\" data-size = \"5\">";
							foreach ($members as $key => $value2)
							{
								echo "<option value = \"".$key."\"";
								
									if ($key === $userTableKey)
									{
										echo " "."selected";
									}
								
								echo ">".$value2."</option>";
							}
						echo "</select>";
					echo "</div>";
				echo "</div>";

				echo "<div class=\"row mb-3\">";
					echo "<label for=\"outsiders\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

					echo "<div class=\"col-sm-10\" id = \"outsiders\">";
						echo $ajax_showActivity[14] ." <input type \"text\" id = \"outsiders\" autocomplete=\"off\">";
					echo "</div>";
				echo "</div>";
				
				echo "<table style = \"width : 100%;\">";
					echo "<tr>";
						echo "<td>";
				
							$sql = "SELECT * FROM ".$table." WHERE (datum <= '".$value['datum']."' AND tid < '".$value['tid']."') OR datum < '".$value['datum']."' ORDER BY datum DESC, tid DESC LIMIT 1"; 
							$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

							while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
							{
								$tableKey = $row['tableKey'];
								echo "<button class = \"btn btn-secondary showActivity\" id = \"aid[".$tableKey."]\" >".$ajax_showActivity[15]."</button>";
							}
					
						echo "</td>";
				
						echo "<td >";
							echo "<button class = \"btn btn-secondary subscribeActivity\" data-replace_table = \"".$replaceTable[PREFIX."activities"]."\">".$ajax_showActivity[16]."</button>";
						echo "</td>";
				
						echo "<td style = \"text-align: right;\">";
				
							$sql = "SELECT * FROM ".$table." WHERE (datum >= '".$value['datum']."' AND tid > '".$value['tid']."') OR datum > '".$value['datum']."' ORDER BY datum ASC, tid ASC LIMIT 1"; 
							$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

							while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
							{
								$tableKey = $row['tableKey'];
								echo "<button class = \"btn btn-secondary showActivity\" id = \"aid[".$tableKey."]\">".$ajax_showActivity[17]."</button>";
							}
							
						echo "</td>";
					echo "</tr>";
				
					echo "<tr>";
						echo "<td colspan = \"20\">";
							echo "<br><br>";
						echo "</td>";
					echo "</tr>";
				
					echo "<tr>";
						echo "<td>";
							echo "&nbsp;";
						echo "</td>";
				
						echo "<td >";
							if ($_SESSION['siteAdmin'] > 0 && (int)$value['canceld'])
							{
								echo "<button class = \"btn btn-success reactivateActivity\">".$ajax_showActivity[19]."</button>";
							}
							else
							{
								echo "<button class = \"btn btn-warning cancelActivity\">".$ajax_showActivity[18]."</button>";
							}
							echo "<input type = \"hidden\" id = \"activityKey\" value = \"".$_POST['id']."\">";
						echo "</td>";
					
						echo "<td>";
							echo "&nbsp;";
						echo "</td>";
					echo "</tr>";
				echo "</table>";
				
				echo "<input type = \"hidden\" id = \"tableKey\" value = \"".$replaceTable[PREFIX."activities"]."\">";
				echo "<input type = \"hidden\" id = \"activityKey\" value = \"".$_POST['id']."\">";
			}
		echo "</form>";
	}
	//echo reset($rowData)['aktivitet'];
echo "</div>"
    

?>