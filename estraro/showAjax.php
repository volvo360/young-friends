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

    if ($replaceTable[$_POST['replaceTable']] === PREFIX.'border_agenda')
    {
        showAjax_border_agenda();
    }
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'border_protocol')
    {
        showAjax_border_protocol();
    }
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'annualMeeting_agenda')
	{
		showAjax_annualMeeting_agenda();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'accounting_year')
	{
		showAjax_accounting_year();
	}
}

function insertDefaultAgenda($tableKey = null)
{
	global $link;
	
	if (empty($tableKey))
	{
		return false;
	}
	
	$table = "`".PREFIX."permBorderAgenda`";
	
	$table2 = "`".PREFIX."border_agenda`";
	
	$sql = "SELECT node.*, (COUNT(parent.lft)) AS depth
                FROM ".$table." AS node,
                        ".$table." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft;";
    echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		$data[] = "<ol>";

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$data[] = "<li>".$row['text']."</li>";
		}
		
		$data[] = "</ol>";
	}
	
	$sql = "UPDATE ".$table2." SET note = '".implode(" ", $data)."' WHERE tableKey = '".$tableKey."'";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	return implode(" ", $data);
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

function showAjax_border_agenda()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_border_agenda = $langStrings['showAjax_border_agenda'];
    
    $replaceTable = getReplaceTable();
	
	$members = getMembers();
    
    $table = "`".PREFIX."border_agenda`";
    
	checkTable($table);
	
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		if (date("Y-m-d") < $row['meetingDay'])
		{
			if (empty($row['note']))
			{
				$row['note'] = insertDefaultAgenda(mysqli_real_escape_string($link, $_POST['id']));
			}
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[1]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo "<input type = date id =\"meetingDay[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."border_agenda"]."\" value = \"".$row['meetingDay']."\">";
				echo "</div>";

					echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[2]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo "<input type = \"time\" id =\"time[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."border_agenda"]."\" value = \"".$row['time']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[3]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<input type = text id =\"place[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."border_agenda"]."\" value = \"".$row['place']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[5]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<div id = \"agendaAreaFiles\">";
						$_POST['replace_agenda'] = $_POST['id'];
						include_once("ajax_syncFiles.php");
						listFiles();
					echo "</div>";
			
					echo "<button class = \"btn btn-secondary uploadFilesBorder\" data-replace_table = \"".$replaceTable[PREFIX."border_agenda"]."\" data-replace_agenda = \"".$_POST['id']."\">".$showAjax_border_agenda[6]."</button>";
				echo "</div>";
			
				
			echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[4]."</label>";


				echo "<div class=\"col-sm-10\">";
					echo "<textarea id =\"note[".$row['tableKey']."]\" class=\"form-control tinyMceArea\" data-replace_table = \"".$replaceTable[PREFIX."border_agenda"]."\">".$row['note']."</textarea>";
				echo "</div>";
			echo "</div>";
		}
		else
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[1]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo $row['meetingDay'];
				echo "</div>";

				echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[2]."</label>";

					echo "<div class=\"col-sm-2\">";
						echo $row['time'];
					echo "</div>";
				echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[3]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo $row['place'];
				echo "</div>";
			echo "</div>";
			
			$_POST['replace_agenda'] = $_POST['id'];
			include_once("ajax_syncFiles.php");
			
			if (countFiles()> 0)
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[5]."</label>";

					echo "<div class=\"col-sm-10\">";
						echo "<div id = \"agendaAreaFiles\">";
							
							listFiles();
						echo "</div>";

				echo "</div>";
			}
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[4]."</label>";


				echo "<div class=\"col-sm-10\">";
					echo $row['note'];
				echo "</div>";
			echo "</div>";
		}
    }
	echo "<input type = \"hidden\" id = \"replace_agenda\" value =\"".$_POST['id']."\">";
}

function showAjax_border_protocol()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_border_protocol = $langStrings['showAjax_border_protocol'];
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."border_agenda`";
	$table2 = "`".PREFIX."border_protocol`";
    
	checkTable($table);
	
    $sql = "SELECT *, t1.tableKey as masterTableKey FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t2.metingId = t1.autoId HAVING masterTableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$table12 = "`".PREFIX."user`";
						
		$tempAdjustment = array_flip(array_map("trim", explode(",", $row['adjustment'])));

		if (is_array($tempAdjustment))
		{
			//Do nothing
		}
		else
		{
			$tempAdjustment[$row['adjustment']] = $row['adjustment'];
		}

		if (empty($row['adjustmentDone']) && (empty($row['secretary']) || (int)$row['secretary'] === $_SESSION['uid']) || $_SESSION['siteAdmin'])
		{
			if (empty($row['note']))
			{
				$temp = insertDefaultProtocol($row['masterTableKey']);
				
				$row['note'] = reset($temp);
				$row['tableKey'] = key($temp);
			}
			
			echo "<form id = \"borderProtocolForm\">";
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[1]."</label>";

					echo "<div class=\"col-sm-3\">";
						echo "<input type = date id =\"meetingDay[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\" value = \"".$row['meetingDay']."\" required>";
					echo "</div>";

					echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[2]."</label>";

						echo "<div class=\"col-sm-2\">";
							echo "<input type = \"time\" id =\"time[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\" value = \"".$row['time']."\">";
						echo "</div>";
					echo "</div>";

				echo "<div class=\"row mb-3\">";
					echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[3]."</label>";

					echo "<div class=\"col-sm-10\">";
						echo "<input type = text id =\"place[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\" value = \"".$row['place']."\" required>";
					echo "</div>";
				echo "</div>";

				echo "<div class=\"row mb-3\">";
					echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[6]."</label>";

					echo "<div class=\"col-sm-10\">";

						$members = getMembers(false);

						$table10 = "`".PREFIX."roles`";
						$table11 = "`".PREFIX."mission`";
						$table12 = "`".PREFIX."user`";	

						if (!empty($row['participant']))
						{
							$participant = array_flip(array_map(trim, explode(",", $row['participant'])));
						}

						$sql = "SELECT *, t12.tableKey as userTableKey FROM (SELECT * FROM ".$table10." WHERE groupId = '2' ORDER BY lft LIMIT 4294967295) as roles INNER JOIN ".$table11." as t11 ON t11.assignment_id = roles.assignment_id INNER JOIN ".$table12." as t12 ON t12.uid = t11.uid ORDER BY roles.lft, t12.firstName, t12.sureName";

						$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
						echo "<select id = \"participant[".$row['tableKey']."]\"class = \"selectpicker show-tick\" data-live-search = \"true\" data-size = \"5\" multiple data-width = \"100%\" required data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\">";
							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								$block[$row2['userTableKey']] = $row2['userTableKey'];

								if (empty($participant))
								{
									echo "<option value = \"".$row2['userTableKey']."\" selected>".$row2['firstName']." ".$row2['sureName']."</option>";
								}
								else
								{
									if (array_key_exists($members[$row2['userTableKey']], $participant))
									{
										echo "<option value = \"".$row2['userTableKey']."\" selected>".$row2['firstName']." ".$row2['sureName']."</option>";
									}
									else
									{
										echo "<option value = \"".$row2['userTableKey']."\">".$row2['firstName']." ".$row2['sureName']."</option>";
									}
								}
							}

							$sql = "SELECT * FROM ".$table12." as t12 WHERE ( NOT tablekey = '".implode("' AND NOT tableKey = '", $block)."') AND betalt > CURDATE() AND uid > 0 ORDER BY t12.firstName, t12.sureName";
							$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

							if (mysqli_num_rows($result2) > 0)
							{
								echo "<option data-divider=\"true\"></option>";
							}

							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								if (array_key_exists($members[$row2['userTableKey']], $participant) && !empty($row['participant']))
								{
									echo "<option value = \"".$row2['userTableKey']."\" selected>".$row2['firstName']." ".$row2['sureName']."</option>";
								}
								else
								{
									echo "<option value = \"".$row2['userTableKey']."\">".$row2['firstName']." ".$row2['sureName']."</option>";
								}
							}

						echo "</select>";

					echo "</div>";
				echo "</div>";

				echo "<div class=\"row mb-3\">";
					echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[7]."</label>";

					echo "<div class=\"col-sm-10\">";
						$table10 = "`".PREFIX."roles`";
						$table11 = "`".PREFIX."mission`";
						$table12 = "`".PREFIX."user`";	

						$members = getMembers(false);

						$sql = "SELECT *, t12.tableKey as userTableKey FROM (SELECT * FROM ".$table10." WHERE groupId = '2' ORDER BY lft LIMIT 4294967295) as roles INNER JOIN ".$table11." as t11 ON t11.assignment_id = roles.assignment_id INNER JOIN ".$table12." as t12 ON t12.uid = t11.uid ORDER BY roles.lft, t12.firstName, t12.sureName";

						$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
						echo "<select class = \"selectpicker show-tick\" id = \"secretary[".$row['tableKey']."]\" data-live-search = \"true\" data-size = \"5\" data-width = \"100%\" required data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\">";
							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								echo "<option value = \"".$row2['userTableKey']."\"";
									if ((int)$members[$row2['userTableKey']] == (int)$row['secretary'])
									{
										echo " "."selected";
									}
									else if ($members[$row2['userTableKey']] === $_SESSION['uid'] && empty($row['secretary']))
									{
										echo " "."selected";
									}

								echo ">".$row2['firstName']." ".$row2['sureName']."</option>";
							}
						echo "</select>";

					echo "</div>";
				echo "</div>";

				echo "<div class=\"row mb-3\">";
					echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[5]."</label>";

					echo "<div class=\"col-sm-10\">";
						$table10 = "`".PREFIX."roles`";
						$table11 = "`".PREFIX."mission`";
						$table12 = "`".PREFIX."user`";	

						$sql = "SELECT *, t12.tableKey as userTableKey FROM (SELECT * FROM ".$table10." WHERE groupId = '2' ORDER BY lft LIMIT 4294967295) as roles INNER JOIN ".$table11." as t11 ON t11.assignment_id = roles.assignment_id INNER JOIN ".$table12." as t12 ON t12.uid = t11.uid ORDER BY roles.lft, t12.firstName, t12.sureName";

						$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
						echo "<select id = \"adjustment[".$row['tableKey']."]\" class = \"selectpicker show-tick\" data-live-search = \"true\" data-size = \"5\" multiple data-width = \"100%\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\">";
							
							$adjustment = array_flip(array_map("trim" , explode(",", $row['adjustment'])));
							while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								echo "<option value = \"".$row2['userTableKey']."\"";
								
									if (array_key_exists($members[$row2['userTableKey']], $adjustment))
									{
										echo " "."selected";
									}
								
								echo ">".$row2['firstName']." ".$row2['sureName']."</option>";
							}
						echo "</select>";

					echo "</div>";
				echo "</div>";

				$_POST['replace_agenda'] = $_POST['id'];
				include_once("ajax_syncFiles.php");

				if (countFiles()> 0)
				{
					echo "<div class=\"row mb-3\">";
						echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[5]."</label>";

						echo "<div class=\"col-sm-10\">";
							echo "<div id = \"agendaAreaFiles\">";

								listFiles();
							echo "</div>";

					echo "</div>";
				}
			
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[4]."</label>";


					echo "<div class=\"col-sm-10\">";
						echo "<textarea id =\"note[".$row['tableKey']."]\" class=\"form-control tinyMceArea\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\" required>".$row['note']."</textarea>";
					echo "</div>";
				echo "</div>";
			
			echo "</from>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<button class = \"btn btn-secondary saveDraft\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\">".$showAjax_border_protocol[8]."</button>";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

				echo "<div class=\"col-sm-10\">";
					if (empty($row['done']))
					{
						echo "<button class = \"btn btn-warning protocolDone\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\">".$showAjax_border_protocol[9]."</button>";
					}
					else if ($row['adjustment'] == $row['adjustmentDone'])
					{
						$table12 = "`".PREFIX."user`";
						
						$temp = array_map("trim", explode(",", $row['adjustment']));
						
						if (is_array($temp))
						{
							$uid = "uid = '".implode("' AND uid ='", $temp)."'";
						}
						else
						{
							$uid = "uid = '".$row['adjustment']."'";
						}
						
						$sql = "SELECT * FROM ".$table12." WHERE ".$uid;
						$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
						while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
						{
							$adjustmentName .= $row2['firstName']." ".$row2['sureName'].", ";
						}
						
						$adjustmentName = trim($adjustmentName,", ");
						
						echo str_replace(array("~~adjustment~~", "~~adjustmentDate~~"), array($adjustmentName, $row['adjustmentDate']),$showAjax_border_protocol[10]);
					}
					else
					{
						echo "<h4>".$showAjax_border_protocol[11]."</h4>";
					}
				echo "</div>";
			echo "</div>";
		}
		else
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[1]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo $row['meetingDay'];
				echo "</div>";

				echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[2]."</label>";

					echo "<div class=\"col-sm-2\">";
						echo $row['time'];
					echo "</div>";
				echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[3]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo $row['place'];
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[6]."</label>";

				echo "<div class=\"col-sm-10\">";
					$table12 = "`".PREFIX."user`";
						
					$temp = array_map("trim", explode(",", $row['participant']));
			
					if (is_array($temp))
					{
						$uid = "uid = '".implode("' OR uid ='", $temp)."'";
					}
					else
					{
						$uid = "uid = '".$row['participant']."'";
					}

					$sql = "SELECT * FROM ".$table12." WHERE ".$uid." ORDER BY firstName, sureName";
					
					$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

					while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						echo $row2['firstName']." ".$row2['sureName']."<br>";
					}
				echo "</div>";
			echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_protocol[4]."</label>";


				echo "<div class=\"col-sm-10\">";
					echo $row['note'];
				echo "</div>";
			echo "</div>";
			
			include_once("ajax_syncFiles.php");

			if (countFiles()> 0)
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_border_agenda[5]."</label>";

					echo "<div class=\"col-sm-10\">";
						echo "<div id = \"agendaAreaFiles\">";

							listFiles();
						echo "</div>";

				echo "</div>";
			}
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

				echo "<div class=\"col-sm-10\">";
					if (empty($row['done']))
					{
						echo "<button class = \"btn btn-warning protocolDone\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\">".$showAjax_border_protocol[9]."</button>";
					}
					else if ($row['adjustment'] == $row['adjustmentDone'])
					{
						$table12 = "`".PREFIX."user`";
						
						$temp = array_map("trim", explode(",", $row['adjustment']));
						
						if (is_array($temp))
						{
							$uid = "uid = '".implode("' AND uid ='", $temp)."'";
						}
						else
						{
							$uid = "uid = '".$row['adjustment']."'";
						}
						
						$sql = "SELECT * FROM ".$table12." WHERE ".$uid;
						$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
						
						while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
						{
							$adjustment .= $row2['firstName']." ".$row2['sureName'].", ";
						}
						
						$adjustment = trim($adjustment,", ");
						
						echo str_replace(array("~~adjustment~~", "~~adjustmentDate~~"), array($adjustment, $row['adjustmentDate']),$showAjax_border_protocol[10]);
					}
					else
					{
						if (array_key_exists($_SESSION['uid'], $tempAdjustment))
						{
							$table12 = "`".PREFIX."user`";
						
							$temp = array_map("trim", explode(",", $row['adjustmentDone']));

							if (is_array($temp))
							{
								$uid = "uid = '".implode("' AND uid ='", $temp)."'";
							}
							else
							{
								$uid = "uid = '".$row['adjustment']."'";
							}

							$sql = "SELECT * FROM ".$table12." WHERE ".$uid;
							$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
							while ($row2 = mysqli_fetch_array($result, MYSQLI_ASSOC))
							{
								$adjustment .= $row2['firstName']." ".$row2['sureName'].", ";
							}

							$adjustment = trim($adjustment,", ");
							if (!empty($adjustment))
							{
								echo "<p>".str_replace(array("~~adjustment~~", "~~adjustmentDate~~"), array($adjustment, $row['adjustmentDate']),$showAjax_border_protocol[10])."</p>";
							}
							
							
							echo "<p><button class = \"btn btn-secondary verifyProtocol\" data-replace_table = \"".$replaceTable[PREFIX."border_protocol"]."\" data-replace_protocol = \"".$row['tableKey']."\">".$showAjax_border_protocol[12]."</button></p>";
						}
						else
						{
							echo "<h4>".$showAjax_border_protocol[11]."</h4>";
						}
					}
				echo "</div>";
			echo "</div>";
		}
    }
	echo "<input type = \"hidden\" id = \"border_agenda_key\" value = \"".$_POST['id']."\">";
}

function insertDefaultAnnualMeetingAgenda($tableKey = null)
{
	global $link;
	
	if (empty($tableKey))
	{
		return false;
	}
	
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
	
	$table = "`".PREFIX."permAnnualMeetingAgenda`";
	
	$table2 = "`".PREFIX."annualMeeting_agenda`";
	
	checkTable($table);
	
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
			$data[] = "<li>".$row['note']."</li>";
		}
		
		$data[] = "</ol>";
	}
	
	$sql = "UPDATE ".$table2." SET note = '".implode(" ", $data)."' WHERE tableKey = '".$tableKey."'";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	echo __LINE__." ".__FILE__." ".$path.$tableKey."/source"."<br>";
	
	mkdir($path."annualmeeting/files/".$tableKey."/source", 0777, true);
	mkdir($path."annualmeeting/files/".$tableKey."/thumbs", 0777, true);
	
	$directory = $path."annualmeeting/files/temp/";
	$scanned_directory = array_diff(scandir($directory), array('..', '.'));
	
	foreach ($scanned_directory as $key => $value)
	{
		rename($directory.$value, $path."annualmeeting/files/".$tableKey."/source/".$value);
	}
	
	return implode(" ", $data);
}
function showAjax_annualMeeting_agenda()
{
    global $link;
    
    $langStrings = getLangstrings();
    $showAjax_annualMeeting_agenda = $langStrings['showAjax_annualMeeting_agenda'];
	
	$members = getMembers();
	$membersRev = getMembers(false);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."annualMeeting_agenda`";
    
	checkTable($table);
	
    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		if ((date("Y-m-d") < $row['meetingDay']) && empty($row['locked']) || $_SESSION['siteAdmin'])
		{
			if (empty($row['note']))
			{
				$row['note'] = insertDefaultAnnualMeetingAgenda(mysqli_real_escape_string($link, $_POST['id']));
			}
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[1]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo "<input type = date id =\"meetingDay[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" value = \"".$row['meetingDay']."\">";
				echo "</div>";

				echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-1 col-form-label\">".$showAjax_annualMeeting_agenda[2]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo "<input type = \"time\" id =\"time[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" value = \"".$row['time']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"responsible[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[8]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<select id =\"responsible[".$row['tableKey']."]\" class=\"selectpicker show-tick\" data-live-search = \"true\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" data-width = \"100%\" data-size = \"5\">";
						echo "<option value = \"-1\">".$showAjax_annualMeeting_agenda[9]."</option>";
						foreach ($members as $key => $value)
						{
							echo "<option value = \"".$key."\"";
								if ((int)$membersRev[$key] == (int)$row['responsible'])
								{
									echo " "."selected";
								}
							echo ">".$value."</option>";
						}
					echo "</select>";
				echo "</div>";
			echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[3]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<input type = text id =\"place[".$row['tableKey']."]\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" value = \"".$row['place']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[5]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo "<div id = \"agendaAreaFiles\">";
						$_POST['replace_agenda'] = $_POST['id'];
						include_once("ajax_syncFiles.php");
						listFilesAnnualMeeting();
					echo "</div>";
			
					echo "<button class = \"btn btn-secondary getReportsAnnualmeeting\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" data-replace_agenda = \"".$_POST['id']."\">".$showAjax_annualMeeting_agenda[12]."</button><br><br>";
			
					echo "<button class = \"btn btn-secondary uploadFilesAnnualmeeting\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" data-replace_agenda = \"".$_POST['id']."\">".$showAjax_annualMeeting_agenda[6]."</button>";
				echo "</div>";
			echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[4]."</label>";


				echo "<div class=\"col-sm-10\">";
					echo "<textarea id =\"note[".$row['tableKey']."]\" class=\"form-control tinyMceArea\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\">".$row['note']."</textarea>";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";


				echo "<div class=\"col-sm-10\">";
					echo "<br><button class=\"form-control btn btn-secondary publicateAnnualMetting\" data-replace_table = \"".$replaceTable[PREFIX."annualMeeting_agenda"]."\" data-annualMetting_key = \"".$_POST['id']."\">".$showAjax_annualMeeting_agenda[7]."</button>";
				echo "</div>";
			echo "</div>";
		}
		else
		{
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"meetingDay[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[1]."</label>";

				echo "<div class=\"col-sm-2\">";
					echo $row['meetingDay'];
				echo "</div>";

				echo "<label for=\"time[".$row['tableKey']."]\" class=\"col-sm-1 col-form-label\">".$showAjax_annualMeeting_agenda[2]."</label>";

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
			
			if (countFilesAnnualMeeting()> 0)
			{
				echo "<div class=\"row mb-3\">";
					echo "<label for=\"place[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[5]."</label>";

					echo "<div class=\"col-sm-10\">";
						echo "<div id = \"agendaAreaFiles\">";
							listFilesAnnualMeeting();
						echo "</div>";

				echo "</div>";
			}
			echo "<div class=\"row mb-3\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_annualMeeting_agenda[4]."</label>";

				echo "<div class=\"col-sm-10\">";
					echo $row['note'];
				echo "</div>";
			echo "</div>";
		}
    }
	echo "<input type = \"hidden\" id = \"replace_agenda\" value =\"".$_POST['id']."\">";
}

function displayIncome()
{
	$langStrings = getLangstrings();
    $displayIncome = $langStrings['displayIncome'];
	
	echo "<select class = \"selectpicker\" id = \"typeIncome\">";
		echo "<option value = \"memberFee\">".$displayIncome[1]."</option>";
		echo "<option value = \"other\">".$displayIncome[2]."</option>";
	echo "</select>";
}

function displayExpence()
{
	$langStrings = getLangstrings();
    $displayExpence = $langStrings['displayExpence'];
	
    echo "<select class = \"selectpicker\" id = \"typeExpence\">";
        echo "<option value = \"domain\">".$displayExpence[2]."</option>";
        echo "<option value = \"hosting\">".$displayExpence[1]."</option>";

        echo "<option value = \"sms\">".$displayExpence[3]."</option>";
        echo "<option value = \"annualmeeting\">".$displayExpence[5]."</option>";
        echo "<option value = \"other\">".$displayExpence[4]."</option>";
    echo "</select>";

    echo "<input id = \"amount\" type = \"number\" min = \"0\" step = \".01\" value = \"161,25\"> kr";
    echo " &nbsp;<span id = \"subSpanExpence\"></span> &nbsp;";
    
}

function displayMembers()
{
	global $link;
	
	$members = getMembers();
	
	echo "<select class = \"selectpicker show-tick\" id = \"membershipFee\" multiple data-live-search = \"true\" data-size = \"5\">";
		$table = "`".PREFIX."user`";
	
		$sql = "SELECT * FROM ".$table." WHERE YEAR(testMember) = '".date("Y")."' ";
		$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
		if (mysqli_num_rows($result) > 0)
		{
			while ($row = mysqli_fetch_array($result))
			{
				echo "<option value = \"".$row['tableKey']."\">".$row['firstName']. " ".$row['sureName']."</option>";
			}
			
			echo "<option data-divider=\"true\"></option>";
		}
	
		$sql = "SELECT * FROM ".$table." WHERE betalt < CURDATE() AND betalt > DATE_SUB(CURDATE(), INTERVAL 3 MONTH) ORDER BY firstName, sureName";
		$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 

		if (mysqli_num_rows($result) > 0)
		{
			while ($row = mysqli_fetch_array($result))
			{
				echo "<option value = \"".$row['tableKey']."\">".$row['firstName']. " ".$row['sureName']."</option>";
			}
			echo "<option data-divider=\"true\"></option>";
		}
	
		foreach ($members as $key => $value)
		{
			echo "<option value = \"".$key."\">".$value."</option>";
		}
	
		$table = "`".PREFIX."user`";
    
	echo "</select>";
}

function showAjax_accounting_year()
{
	global $link;
	
	global $link;
	
	$langStrings = getLangstrings();
    $showAjax_accounting_year = $langStrings['showAjax_accounting_year'];
	
	$members = getMembers();
	$membersRev = getMembers(false);
    
    $replaceTable = getReplaceTable();
    
	$table = "`".PREFIX."accounting_year`";
    $table2 = "`".PREFIX."accounting`";
	
	$table11 = "`".PREFIX."roles`";
	$table12 = "`".PREFIX."mission`";
	
	$th['verifierId'] = $showAjax_accounting_year[8];
	$th['note'] = $showAjax_accounting_year[9];
	$th['income'] = $showAjax_accounting_year[10];
	$th['expence'] = $showAjax_accounting_year[11];
    $th['vertificateFiles'] = $showAjax_accounting_year[22];
	
	$sql = "SELECT * FROM ".$table11." t11 INNER JOIN ".$table12." t12 ON t11.assignment_id = t12.assignment_id WHERE extra = 'cashier'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cashier = $row['uid'];
	}
	
	$sql = "SELECT * FROM ".$table11." t11 INNER JOIN ".$table12." t12 ON t11.assignment_id = t12.assignment_id WHERE extra = 'auditor'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$auditor[$row['uid']] = $row['uid'];
	}
    
	$sql = "SELECT * FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t1.autoId = t2.accounting WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' LIMIT 1";
    //echo __LINE__." ".$sql."<br>";
	
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$activeYear = $row['year'];
		$status = $row['status'];
		
		$revisionDate = $row['revisionDate'];
		$revision = $row['revisionBy'];
	}
	
	echo "<form id = \"accountForm\">";
		if (((int)$cashier == (int)$_SESSION['uid'] || $_SESSION['siteAdmin']) && $status == "active")
		{
			if ((date("Y") > $activeYear) &&  $status == "active")
			{
				echo $showAjax_accounting_year[16]."<br><br>";
				
				echo "<button class = \"btn btn-secondary closeFiscalYear\" data-replace_table = \"".$replaceTable[PREFIX."accounting_year"]."\" data-replace_key = \"".$_POST['id']."\" data-target_div = \"ajax_".$replaceTable[PREFIX."accounting_year"]."\">".$showAjax_accounting_year[17]."</button><br><br>";
			}
			
			echo "<select class = \"selectpicker\" id = \"accountType\">";
				echo "<option value = \"income\">".$showAjax_accounting_year[2]."</option>";
				echo "<option value = \"expence\">".$showAjax_accounting_year[3]."</option>";
			echo "</select>";
		
			echo " &nbsp;".$showAjax_accounting_year[4]." ";


			echo "<span id = \"defaultType\">";
				displayIncome();
				echo " &nbsp;".$showAjax_accounting_year[5]." ";
				echo "<span id = \"subSpanIncome\">";
					displayMembers();
				echo "</span>";
				echo " ";
				echo "<input type = number min = 0 step = \".01\" value = \"100\" id = \"amount\"> kr";
			echo "</span>";
			echo "<br><br>";

			echo "<div id = \"verifiedFileArea\"></div>";
			
			echo "<button class = \"btn btn-secondary addVerified\" data-replace_table = \"".$replaceTable[PREFIX."accounting_year"]."\" data-replace_key = \"".$_POST['id']."\">".$showAjax_accounting_year[13]."</button><br><br>";
			
			echo $showAjax_accounting_year[7]." <input type = \"date\" min = \"".$activeYear."-01-01"."\" max = \"".$activeYear."-12-31"."\"  value = \"".date("Y-m-d")."\" id = \"accountDate\"><br><br>";

			echo "<button class = \"btn btn-secondary form-control addPostAccounting\" >".$showAjax_accounting_year[6]."</button><br><br>";
		}
		else if (((int)$cashier == (int)$_SESSION['uid']) && $status == "locked" && empty($revisionDate))
		{
			if (empty($revision))
			{
				echo "<button class = \"btn btn-secondary form-control requestRevision\" data-target_div = \"ajax_".$replaceTable[PREFIX."accounting_year"]."\" data-account_key = \"".$_POST['id']."\" >".$showAjax_accounting_year[19]."</button><br><br>";
			}
			else
			{
				echo "<h4>".$showAjax_accounting_year[20]."</h4>";
			}
		}
		else if (($_SESSION['siteAdmin'] || array_key_exists($_SESSION['uid'])) && $status == "locked" && empty($revisionDate))
		{
			echo "<button class = \"btn btn-secondary form-control revisionDone\" data-replace_table = \"".$replaceTable[PREFIX."accounting_year"]."\" data-account_key = \"".$_POST['id']."\" >".$showAjax_accounting_year[21]."</button><br><br>";
			
		}
	echo "</form>";
	
	checkTable($table);
	
	$sql = "SELECT * FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t1.autoId = t2.accounting WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' LIMIT 1";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		echo $showAjax_accounting_year[1]." ".$row['startBalance']."<br><br>";
	}
	
    $sql = "SELECT * FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t1.autoId = t2.accounting WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' ORDER BY t2.autoId DESC";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
    
	$rowCount = (int)mysqli_num_rows($result);
	
	$i = $rowCount;
	
	echo "<div id = \"areaAccounting\">";
	
        echo "<table id = \"accounting\" class = \"DataTable table table-striped\" style=\"width:100%\">";

        echo "<thead>";
            echo "<tr>";
                foreach ($th as $key => $value)
                {
                    echo "<td id = \"".$key."\">".$value."</td>";
                }
            echo "</tr>";
        echo "<thead>";

        echo "<tbody>";
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                if ($status == "active")
                {
                    echo "<tr class = editAccountingPost data-replace_key = \"".$row['tableKey']."\">";
                }
                else
                {
                    echo "<tr>";
                }
                    foreach ($th as $key => $value)
                    {
                        if ($key == "verifierId")
                        {
                            echo "<td>".$i."</td>";
                        }
                        else if ($key == "vertificateFiles")
                        {
                            if (!empty($row[$key]))
                            {
                                echo "<td class = \"showVerificatFile\" data-show_file = \"".$row['tableKey']."\"><i class=\"fas fa-file-alt\"></i> ".$row[$key]."</td>";
                            }
                            else
                            {
                                echo "<td>".$row[$key]."</td>";
                            }

                        }
                        else
                        {
                            echo "<td>".$row[$key]."</td>";
                        }
                    }
                echo "</tr>";


                $i--;
            }
        echo "</tbody>";
        echo "</table>";
	
	echo "</div>";
	
	$sql = "SELECT SUM(income) as income, SUM(expence) as expence FROM ".$table." t1 LEFT OUTER JOIN ".$table2." t2 ON t1.autoId = t2.accounting WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' LIMIT 1";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if (empty($row['income']))
		{
			echo $showAjax_accounting_year[14]." 0<kr><br>";
		}
		else
		{
			echo $showAjax_accounting_year[14]." ".$row['income']."<kr><br>";
		}
		if (empty($row['expence']))
		{
			echo $showAjax_accounting_year[15]." 0<kr><br>";
		}
		else
		{
			echo $showAjax_accounting_year[15]." -".$row['expence']."<kr><br>";
		}
		
		$sum = (float)$row['startBalance']+(float)$row['income']-(float)$row['expence'];
		
		if ($status == "locked")
		{
			if ($sum < 0)
			{
				echo $showAjax_accounting_year[18]." <b style = \"color : red;\">".$sum." kr</b><br><br>";
			}
			else
			{
				echo $showAjax_accounting_year[18]." <b>".$sum." kr</b><br><br>";
			}
		}
		else
		{
			if ($sum < 0)
			{
				echo $showAjax_accounting_year[12]." <b style = \"color : red;\">".$sum." kr</b><br><br>";
			}
			else
			{
				echo $showAjax_accounting_year[12]." <b>".$sum." kr</b><br><br>";
			}
		}
		
		if (($_SESSION['siteAdmin'] || array_key_exists($_SESSION['uid'])) && $status == "locked" && empty($revisionDate))
		{
			echo "<button class = \"btn btn-secondary form-control revisionDone\" data-replace_table = \"".$replaceTable[PREFIX."accounting_year"]."\" data-account_key = \"".$_POST['id']."\" >".$showAjax_accounting_year[21]."</button><br><br>";
			
		}
	}
}

?>