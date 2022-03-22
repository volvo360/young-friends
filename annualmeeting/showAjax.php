<?php

session_start();

error_reporting(E_ALL);

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

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
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
	
    $replaceTable = getReplaceTable(false);

    if ($replaceTable[$_POST['replaceTable']] === PREFIX.'annualMeeting_agenda')
	{
		showAjax_annualMeeting_agenda();
	}
}


function insertDefaultProtocol($tableKey = null)
{
	global $link;
	
	if (empty($tableKey))
	{
		return false;
	}
	
	$table = "`".PREFIX."permBorderProtocol`";
	
	$table2 = "`".PREFIX."border_protocol`";
	
	$table3 = "`".PREFIX."border_agenda`";
	
	$table10 = "`".PREFIX."roles`";
	$table11 = "`".PREFIX."mission`";
	$table12 = "`".PREFIX."user`";	
	
	$sql = "SELECT * FROM (SELECT * FROM ".$table10." WHERE groupId = '2' ORDER BY lft LIMIT 1) as roles INNER JOIN ".$table11." as t11 ON t11.assignment_id = roles.assignment_id INNER JOIN ".$table12." as t12 ON t12.uid = t11.uid";
	
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$chairman = $row['firstName']." ".$row['sureName'];
	}
	
	$sql = "SELECT node.*, (COUNT(parent.lft)) AS depth
                FROM ".$table." AS node,
                        ".$table." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft;";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		$data[] = "<ol>";

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$row['text'] = str_replace("~~chairman~~", $chairman, $row['text']);
			$data[] = "<li>".$row['text']."</li>";
		}
		
		$data[] = "</ol>";
	}
	
	$sql = "SELECT * FROM ".$table3." WHERE tableKey = '".$tableKey."'";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$metingId = $row['autoId'];
	}
	
	$sql = "INSERT INTO ".$table2." (metingId, note) VALUES ('".$metingId."', '".implode(" ", $data)."')";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	$temp = checkTable($table2);
	
	$res[$temp] = implode(" ", $data);
	
	
	
	return $res;
}

function displayAgenda($row = null)
{
	$langStrings = getLangstrings();
    $showAjax_annualMeeting_agenda = $langStrings['showAjax_annualMeeting_agenda'];
	
	echo "<div class=\"row mb-3\">";
			echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[1]."</label>";

			echo "<div class=\"col-sm-2\">";
				echo $row['meetingDay'];
			echo "</div>";

			echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[2]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo $row['time'];
				echo "</div>";
			echo "</div>";

		echo "<div class=\"row mb-3\">";
			echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[3]."</label>";

			echo "<div class=\"col-sm-10\">";
				echo $row['place'];
			echo "</div>";
		echo "</div>";

		$_POST['replace_agenda'] = $_POST['id'];
		include_once("ajax_syncFiles.php");

		/*if (countFiles()> 0)
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[5]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<div id = \"agendaAreaFiles\">";

						listFiles();
					echo "</div>";

			echo "</div>";
		}*/
		echo "<div class=\"row mb-3\">";
			echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[4]."</label>";


			echo "<div class=\"col-sm-10\">";
				echo $row['note'];
			echo "</div>";
		echo "</div>";
}

function showAjax_annualMeeting_agenda()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_annualMeeting_agenda = $langStrings['showAjax_annualMeeting_agenda'];
    
    $replaceTable = getReplaceTable();
	
	$members = getMembers();
    
    $table = "`".PREFIX."annualMeeting_agenda`";
	$table2 = "`".PREFIX."annualMeeting_protocol`";
	
	checkTable($table);
	
    $sql = "SELECT * FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t1.meetingId = t2.meetingId WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$sql = "SELECT * FROM ".$table." t1 WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
		$result2= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

		while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			$temp = $row2;
		}
		
		if (!empty($row['note']) || $_SESSION['border'] || $_SESSION['siteAdmin'])
		{
			echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
				echo "<li class=\"nav-item\" role=\"presentation\">";
					echo "<button class=\"nav-link active\" id=\"protocol-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#protocol\" type=\"button\" role=\"tab\" aria-controls=\"protocol\" aria-selected=\"true\">".$showAjax_annualMeeting_agenda[10]."</button>";
				echo "</li>";
				echo "<li class=\"nav-item\" role=\"presentation\">";
					echo "<button class=\"nav-link\" id=\"agenda-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#agenda\" type=\"button\" role=\"tab\" aria-controls=\"agenda\" aria-selected=\"false\">".$showAjax_annualMeeting_agenda[11]."</button>";
				echo "</li>";
			echo "</ul>";
			
			echo "<div class=\"tab-content\" id=\"myTabContent\" >";
				echo "<br>";
				echo "<div class=\"tab-pane fade show active\" id=\"protocol\" role=\"tabpanel\" aria-labelledby=\"protocol-tab\" style = \"height : 90%;\">";
					showAjax_annualmeeting_protocol();
				echo"</div>";
				echo "<div class=\"tab-pane fade\" id=\"agenda\" role=\"tabpanel\" aria-labelledby=\"agenda-tab\" style = \"height : 90%;\">";
					displayAgenda($temp);
				echo"</div>";
			echo "</div>";
		}
		else
		{
			displayAgenda($temp);
		}
	}
	
	echo "<input type = \"hidden\" id = \"replace_agenda\" value =\"".$_POST['id']."\">";
}

function showAjax_annualmeeting_protocol()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_annualmeeting_protocol = $langStrings['showAjax_annualmeeting_protocol'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."annualMeeting_agenda`";
	$table2 = "`".PREFIX."annualMeeting_protocol`";
    
	$table10 = "`".PREFIX."roles`";
	$table11 = "`".PREFIX."mission`";
	
	$table20 = "`".PREFIX."activities`";
	$table21 = "`".PREFIX."registered`";
	
	$table30 = "`".PREFIX."user`";
	
	checkTable($table);
	checkTable($table2);
	
    $sql = "SELECT *, t1.tableKey as masterTableKey FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t2.meetingId = t1.meetingId HAVING masterTableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
	$members = getMembers();
	
	$members2 = getMembers(false);
	
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$temp = array_map("trim", explode(",", $row['adjustment']));
			
		if (is_array($temp))
		{
			foreach ($temp as $key => $value)
			{
				$adjusters[(int)$value] = (int)$value;
			}
		}
		else
		{
			$adjusters[(int)$row['adjustment']] = (int)$row['adjustment'];
		}
		
		if (empty($row['note']) &&($_SESSION['border'] || $_SESSION['siteAdmin']))
		{
			$sql = "SELECT * FROM ".$table10." t10 INNER JOIN ".$table11." t11 ON t10.assignment_id = t11.assignment_id WHERE extra IS NOT NULL";
			$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

			while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
			{
				$mission[$row2['extra']] = $row2['uid'];
			}
			
			echo "<form id = \"protocolForm\">";
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"chairman\" class=\"col-sm-4 col-form-label\">".$showAjax_annualmeeting_protocol[1]."</label>";
				
					echo "<div class=\"col-sm-8\">";
						echo "<select class = \"selectpicker show-tick\" id=\"chairman\" data-width = \"100%\" data-size = \"5\">";
							foreach ($members as $key => $member)
							{
								echo "<option value = \"".$key."\"";
									if ($members2[$key] == $mission['chairman'])
									{
										echo " "."selected";
									}
								echo ">".$member."</option>";
							}
						echo "</select>";
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"secretery\" class=\"col-sm-4 col-form-label\">".$showAjax_annualmeeting_protocol[2]."</label>";
				
					echo "<div class=\"col-sm-8\">";
						echo "<select class = \"selectpicker show-tick\" id=\"secretery\" data-width = \"100%\" data-size = \"5\">";
							foreach ($members as $key => $member)
							{
								echo "<option value = \"".$key."\"";
									if ($members2[$key] == $mission['secretery'])
									{
										echo " "."selected";
									}
								echo ">".$member."</option>";
							}
						echo "</select>";
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"adjuster\" class=\"col-sm-4 col-form-label\">".$showAjax_annualmeeting_protocol[3]."</label>";
				
					echo "<div class=\"col-sm-8\">";
						echo "<select class = \"selectpicker show-tick\" id=\"adjuster\" data-width = \"100%\" data-size = \"5\">";
							foreach ($members as $key => $member)
							{
								echo "<option value = \"".$key."\"";
									if ($members2[$key] == $mission['nominee'])
									{
										echo " "."selected";
									}
								echo ">".$member."</option>";
							}
						echo "</select>";
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"adjuster\" class=\"col-sm-4 col-form-label\">"."&nbsp;"."</label>";
				
					echo "<div class=\"col-sm-8\">";
						echo "<button class = \"btn btn-secondary form-control insertDefaultAnnualprotocol\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\">".$showAjax_annualmeeting_protocol[4]."</button>";
					echo "</div>";
				echo "</div>";
			echo "</form>";
		}
		else if (($_SESSION['uid'] == $row['secretary']  && (int)$row['done'] <= 1)|| $_SESSION['siteAdmin'])
		{
			echo "<h3>".$showAjax_annualmeeting_protocol[6]."</h3>";
			echo "<form>";
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"plats[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[5]."</label>";
					
					echo "<div class=\"col-sm-10\">";
						echo "<input type=\"text\" class=\"form-control syncData\" id=\"plats[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\" value = \"".$row['place']."\">";
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[7]."</label>";
					
					echo "<div class=\"col-sm-2\">";
						echo "<input type=\"date\" min = \"".date("Y")."01-01"."\" class=\"form-control syncData\" id=\"meetingDay[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\" value = \"".$row['meetingDay']."\">";
					echo "</div>";
			
					echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-1 col-form-label\">".$showAjax_annualmeeting_protocol[8]."</label>";
					
					echo "<div class=\"col-sm-2\">";
						echo "<input type=\"time\" class=\"form-control syncData\" id=\"meetingDay[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\" value = \"".substr($row['time'], 0,5)."\">";
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"plats[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[9]."</label>";
					
					echo "<div class=\"col-sm-10\">";
						$sql = "SELECT *, t30.tableKey userTableKey FROM (SELECT * FROM ".$table20." WHERE datum = '".$row['meetingDay']."' AND tid = '".$row['time']."') as t20 INNER JOIN ".$table21." t21 ON t20.aid = t21.aid INNER JOIN ".$table30." t30 ON t30.uid = t21.uid ORDER BY firstName, sureName";
			
						$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

						while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
						{
							$registered[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
						}
			
						$arrayKeys = array_keys($registered);
			
						if (is_array($registered))
						{
							$sql = "SELECT * FROM ".$table30." WHERE NOT tableKey = '".implode("' AND NOT tableKey = '", $arrayKeys)."' AND betalt > CURDATE() AND uid > 0 ORDER BY firstName, sureName";
						}
						else
						{
							$sql = "SELECT * FROM ".$table30." WHERE betalt > CURDATE() AND uid > 0 ORDER BY firstName, sureName";
						}
						$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

						while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
						{
							$members4[$row2['tableKey']] = $row2['firstName']." ".$row2['sureName'];
						}
			
						echo "<select class = \"selectpicker show-tick\" id = participant[".$row['tableKey']."] data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\" data-live-search = \"true\" data-size = \"5\" multiple data-width = \"100%\">";
							foreach ($registered as $key => $value)
							{
								echo "<option value = \"".$key."\" selected>".$value."</value>";
							}
							echo "<option data-divider=\"true\"></option>";
							foreach ($members4 as $key => $value)
							{
								echo "<option value = \"".$key."\">".$value."</value>";
							}
						echo "</select>";
			
					echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"secretary[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[2]."</label>";
			
				echo "<div class=\"col-sm-10\">";
					echo "<select class = \"selectpicker show-tick\" id = secretary[".$row['tableKey']."] data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\" data-live-search = \"true\" data-size = \"5\"  data-width = \"100%\">";
						foreach ($members as $key => $value)
						{
							echo "<option value = \"".$key."\"";
								if ((int)$members2[$key] == (int)$row['secretary'])
								{
									echo " "."selected";
								}
							echo ">".$value."</value>";
						}
					echo "</select>";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"adjustment[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[3]."</label>";
			
				echo "<div class=\"col-sm-10\">";
					echo "<select class = \"selectpicker show-tick\" id = adjustment[".$row['tableKey']."] data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\" data-live-search = \"true\" data-size = \"5\" multiple data-width = \"100%\">";
			
						
			
						foreach ($members as $key => $value)
						{
							echo "<option value = \"".$key."\"";
								if (array_key_exists((int)$members2[$key], $adjusters ))
								{
									echo " "."selected";
								}
							echo ">".$value."</value>";
						}
					echo "</select>";
				echo "</div>";
			echo "</div>";
			
			include_once("ajax_syncFiles.php");
			$_POST['replace_agenda'] = $_POST['id'];
			if (countFiles() > 0)
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[10]."</label>";

					echo "<div class=\"col-sm-10\">";
						listFiles();
					echo "</div>";
				echo "</div>";
			}
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[5]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<textarea class=\"tinyMceArea\" id=\"note[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\">"; 
						echo $row['note'];
					echo "</textarea>";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<button class = \"btn btn-secondary form-control annualmeetingProtocolDone\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\">"; 
						echo $showAjax_annualmeeting_protocol[11];
					echo "</button>";
				echo "</div>";
			echo "</div>";
		}
		else if (array_key_exists($_SESSION['uid'], $adjusters ) && (int)$row['done'] == 1)
		{
			echo "<h3>".$showAjax_annualmeeting_protocol[6]."</h3>";
			echo "<form>";
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"plats[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[5]."</label>";
					
					echo "<div class=\"col-sm-10\">";
						echo $row['place'];
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[7]."</label>";
					
					echo "<div class=\"col-sm-2\">";
						echo $row['meetingDay'];
					echo "</div>";
			
					echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-1 col-form-label\">".$showAjax_annualmeeting_protocol[8]."</label>";
					
					echo "<div class=\"col-sm-2\">";
						echo substr($row['time'], 0,5);
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"plats[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[9]."</label>";
					
					echo "<div class=\"col-sm-10\">";
						
						if (empty($row['participant']))
						{
							$sql = "SELECT *, t30.tableKey userTableKey FROM (SELECT * FROM ".$table20." WHERE datum = '".$row['meetingDay']."' AND tid = '".$row['time']."') as t20 INNER JOIN ".$table21." t21 ON t20.aid = t21.aid INNER JOIN ".$table30." t30 ON t30.uid = t21.uid ORDER BY firstName, sureName";

							$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								$registered[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
							}
						}
						else
						{
							$temp2 = array_keys(array_map("trim", explode(",", $row['participant'])));
							
							if (!is_array($temp2))
							{
								$temp2[] = $row['participant'];
							}
							
							$sql = "SELECT * FROM ".$table30." WHERE uid = '".implode("' OR uid ='", $temp2)."' ORDER BY firstName, sureName";
							$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								$registered[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
							}
						}
						echo implode(", ", $registered);
					echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"secretary[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[2]."</label>";
			
				echo "<div class=\"col-sm-10\">";
					$sql = "SELECT * FROM ".$table30." WHERE uid = '".$row['secretary']."' ORDER BY firstName, sureName";
					$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

					while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						echo $row2['firstName']." ".$row2['sureName'];
					}
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"adjustment[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[3]."</label>";
			
				echo "<div class=\"col-sm-10\">";
			
					$temp2 = (array_map("trim", explode(",", $row['adjustment'])));
							
					if (!is_array($temp2))
					{
						$temp2[] = $row['adjustment'];
					}

					$sql = "SELECT * FROM ".$table30." WHERE uid = '".implode("' OR uid ='", $temp2)."' ORDER BY firstName, sureName";
					$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

					while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						$adjustment[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
					}
					echo implode(", ", $adjustment);
				echo "</div>";
			echo "</div>";
			
			include_once("ajax_syncFiles.php");
			$_POST['replace_agenda'] = $_POST['id'];
			if (countFiles() > 0)
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[10]."</label>";

					echo "<div class=\"col-sm-10\">";
						listFiles();
					echo "</div>";
				echo "</div>";
			}
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[5]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo $row['note'];
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

				echo "<div class=\"col-sm-10\">";
					$temp2 = array_map("trim", explode(",", $row['adjustmentDone']));
	
					if (!is_array($temp2))
					{
						$temp2[] = $row['adjustmentDone'];
					}
			
					if (array_search($_SESSION['uid'], $temp2) === false)
					{
							echo "<button class = \"btn btn-secondary form-control verifyAnnualmeetingProtocol\" data-replace_protocol = \"".$_POST['id']."\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_protocol"]."\">"; 
							echo $showAjax_annualmeeting_protocol[12];
						echo "</button>";
					}
					else
					{
						echo $showAjax_annualmeeting_protocol[14];
					}
					
				echo "</div>";
			echo "</div>";
		}
		else if ((int)$row['done'] >= 2)
		{
			echo "<h3>".$showAjax_annualmeeting_protocol[6]."</h3>";
			
			echo "<div class=\"row mb-3\">";
					echo "<label for=\"plats[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[5]."</label>";
					
					echo "<div class=\"col-sm-10\">";
						echo $row['place'];
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[7]."</label>";
					
					echo "<div class=\"col-sm-2\">";
						echo $row['meetingDay'];
					echo "</div>";
			
					echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-1 col-form-label\">".$showAjax_annualmeeting_protocol[8]."</label>";
					
					echo "<div class=\"col-sm-2\">";
						echo substr($row['time'], 0,5);
					echo "</div>";
				echo "</div>";
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"plats[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[9]."</label>";
					
					echo "<div class=\"col-sm-10\">";
						
						if (empty($row['participant']))
						{
							$sql = "SELECT *, t30.tableKey userTableKey FROM (SELECT * FROM ".$table20." WHERE datum = '".$row['meetingDay']."' AND tid = '".$row['time']."') as t20 INNER JOIN ".$table21." t21 ON t20.aid = t21.aid INNER JOIN ".$table30." t30 ON t30.uid = t21.uid ORDER BY firstName, sureName";

							$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								$registered[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
							}
						}
						else
						{
							$temp2 = array_keys(array_map("trim", explode(",", $row['participant'])));
							
							if (!is_array($temp2))
							{
								$temp2[] = $row['participant'];
							}
							
							$sql = "SELECT * FROM ".$table30." WHERE uid = '".implode("' OR uid ='", $temp2)."' ORDER BY firstName, sureName";
							$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								$registered[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
							}
						}
						echo implode(", ", $registered);
					echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"secretary[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[2]."</label>";
			
				echo "<div class=\"col-sm-10\">";
					$sql = "SELECT * FROM ".$table30." WHERE uid = '".$row['secretary']."' ORDER BY firstName, sureName";
					$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

					while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						echo $row2['firstName']." ".$row2['sureName'];
					}
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"adjustment[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[3]."</label>";
			
				echo "<div class=\"col-sm-10\">";
			
					$temp2 = (array_map("trim", explode(",", $row['adjustment'])));
							
					if (!is_array($temp2))
					{
						$temp2[] = $row['adjustment'];
					}

					$sql = "SELECT * FROM ".$table30." WHERE uid = '".implode("' OR uid ='", $temp2)."' ORDER BY firstName, sureName";
					$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

					while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						$adjustment[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
					}
					echo implode(", ", $adjustment);
				echo "</div>";
			echo "</div>";
			
			include_once("ajax_syncFiles.php");
			$_POST['replace_agenda'] = $_POST['id'];
			if (countFiles() > 0)
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[10]."</label>";

					echo "<div class=\"col-sm-10\">";
						listFiles();
					echo "</div>";
				echo "</div>";
			}
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualmeeting_protocol[5]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo $row['note'];
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

				echo "<div class=\"col-sm-10\">";
					$temp2 = (array_map("trim", explode(",", $row['adjustment'])));
							
					if (!is_array($temp2))
					{
						$temp2[] = $row['adjustment'];
					}

					$sql = "SELECT * FROM ".$table30." WHERE uid = '".implode("' OR uid ='", $temp2)."' ORDER BY firstName, sureName";
					$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

					while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						$adjustment[$row2['userTableKey']] = $row2['firstName']." ".$row2['sureName'];
					}
					echo $showAjax_annualmeeting_protocol[13] ." ".implode(", ", $adjustment). " den ".$row['adjustmentDate'];
				echo "</div>";
			echo "</div>";
		}
    }
	echo "<input type = \"hidden\" id = \"border_agenda_key\" value = \"".$_POST['id']."\">";
}
?>