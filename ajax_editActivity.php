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
include_once($path."common/emailTemplate.php");
include_once($path."common/send_mail.php");

function renderActivityHeader()
{
	$langStrings = getLangstrings();
	$renderActivityHeader = $langStrings['renderActivityHeader'];
	
	if (!empty($_POST['editActivity']) || !empty($_POST['id']))
	{
		echo "<h1>".$renderActivityHeader[1]."</h1>";
	}
	else
	{
		echo "<h1>".$renderActivityHeader[2]."</h1>";
	}
}

function renderActityBody()
{
	global $link;
	
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
	
	$langStrings = getLangstrings();
	$renderActityBody = $langStrings['renderActityBody'];
	
	$replaceTable = getReplaceTable();
	
	$members = getMembers();
	
	$table = "`".PREFIX."activities`";
	$table2 = "`".PREFIX."places`";
	$table10 = "`".PREFIX."user`";
	
	//checkTable($table);
	
	if (!empty($_POST['id']) || $_POST['editActivity'] !== -1)
	{
		if (!empty($_POST['id']))
		{
			$_POST['editActivity'] = $_POST['id'];
		}
		
		$sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['editActivity'])."' ";
		$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$rowData = $row;
		}
	}
	
	$sql = "SELECT node.*, (COUNT(parent.lft) - 1) AS depth
				FROM ".$table2." AS node,
						".$table2." AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				GROUP BY node.lft
				ORDER BY node.lft;";
	$result = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$place[] = $row;
	}
    
    if (!empty($_POST['editActivity']) || !empty($_POST['id']))
    {
        echo "<h5 id = \"infoText\">".$renderActityBody[19]."</h5><br>";
        echo "<input type = \"hidden\" id = \"defaultInfoText\" value = \"".$renderActityBody[19]."\">";
        echo "<input type = \"hidden\" id = \"updateInfoText\" value = \"".$renderActityBody[20]."\">";
    }
	if (empty($rowData['tableKey']))
	{
		echo "<form id = \"addActivityForm\">";
	}
		echo "<div class=\"row mb-3\">";

			echo "<label for=\"rubrik";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[1]."</label>";

			echo "<div class=\"col-sm-10\">";
				echo "<input type=\"text\" class=\"form-control";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" id=\"rubrik";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\"";
				if (!empty($rowData['rubrik']))
					{
						echo " "."value = \"".$rowData['rubrik']."\"";
					}
			echo " required>";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"aktivitet";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[2]."</label>";

			echo "<div class=\"col-sm-10\">";
				echo "<textarea class=\"form-control tinyMceArea\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" class = \"tinyMceArea\" id=\"aktivitet";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\" required>";
					if (!empty($rowData['tableKey']))
					{
						echo $rowData['aktivitet'];
					}
				echo "</textarea>";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"datum";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[4]."</label>";

			echo "<div class=\"col-sm-2\">";
	
				echo "<input type=\"date\" class=\"form-control ";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" id=\"datum";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\"";
				if (!empty($rowData['datum']))
				{
					echo " "."value = \"".$rowData['datum']."\"";
				}
				else if ($_POST['date'])
				{
					$date = mysqli_real_escape_string($link, $_POST['date']);
					echo " "."value = \"".$date."\"";
				}
				else
				{
					$date = date("Y-m-d", strtotime('next saturday'));
					
					$run = true;
					do 
					{
						$sql = "SELECT * FROM ".$table." WHERE datum = '".$date."'";
						$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
						
						if (mysqli_num_rows($result) == 0)
						{
							$run = false;
						}
						else
						{
							$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " +1 week"));

						}
					} while ($run);
					
					echo " " ."value = \"".$date."\"";
				}
				echo " required>";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"tid";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[5]."</label>";

			echo "<div class=\"col-sm-2\">";
				echo "<input type=\"time\" class=\"form-control ";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" id=\"tid";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\"";
				if (!empty($rowData['tid']))
					{
						echo " "."value = \"".$rowData['tid']."\"";
					}
			echo ">";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"ansvarig";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[7]."</label>";

			echo "<div class=\"col-sm-2\">";
				echo "<select class=\"selectpicker show-tick\" data-live-search = \"true\" data-size = \"5\"";
					if (!empty($rowData['tableKey']))
					{
						echo "data-replace_table = \"".$replaceTable[PREFIX.'activities']."\"";
					} 
					echo "id=\"ansvarig";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\">";
					if (!empty($rowData['ansvarig']) || (empty($rowData['ansvarig']) && array_key_exists('ansvarig', $rowData)))
					{
						if (empty($rowData['ansvarig']))
						{
							$sql = "SELECT * FROM ".$table10." WHERE uid = '0'";
							array_unshift($members,"Young Friends");
						}
						else
						{
							$sql = "SELECT * FROM ".$table10." WHERE uid = '".mysqli_real_escape_string($link, $rowData['ansvarig'])."'";	
						}
					}
					else
					{
						$sql = "SELECT * FROM ".$table10." WHERE uid = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
					}
					$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						$userKey = $row['tableKey'];
					}	

					foreach ($members as $key => $value)
					{
						echo "<option value = \"".$key."\"";
							if ($key == $userKey)
							{
								echo " "."selected";
							}
						echo ">".$value."</option>";
					}
				echo "</select>";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"pris";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[3]."</label>";

			echo "<div class=\"col-sm-10\">";

				echo "<input type=\"number\" min = \"0\" class=\"";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" size = 10 id=\"pris";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\"";
				if (!empty($rowData['pris']))
					{
						echo " "."value = \"".$rowData['pris']."\"";
					}
				echo "> kr. ";

			echo " <br><br>";
				echo "<div class=\"form-check checkbox-slider--b\">";
					echo "<label>";
						echo "<input type=\"checkbox\" id = \"pree_pay";
							if (!empty($rowData['tableKey']))
							{
								echo "[".$rowData['tableKey']."]";
							}
						echo "\" class = \"";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" ";
							if (!empty($rowData['pree_pay']))
							{
								echo " "."checked";
							}
						echo "><span> ".$renderActityBody[11]."</span>";
					echo "</label>";
				echo "</div>";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"plats";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[6]."</label>";

			echo "<div class=\"col-sm-5\">";
				echo "<input type=\"text\" class=\"form-control ";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" id=\"plats";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\"";
				if (!empty($rowData['plats']))
					{
						echo " "."value = \"".$rowData['plats']."\"";
					}
			echo ">";
			echo "</div>";

			echo "<div class=\"col-sm-5\">";

				$first = true;
				$end = false;

				$oldDepth = 0;
				echo "<select class = \"selectpicker\" data-size = \"5\" data-live-search = \"true\" data-width = \"100%\" id = \"predefinedPlace\">";
					foreach ($place as $key => $value)
					{
						if (((int)$value['lft'] + 1) < (int)$value['rgt'])
						{
							if ($first)
							{
								$first = false;
								$end = true;
							}
							else
							{
								echo "</optgroup>";
							}

							//echo "<optgroup label = \"".$value['note']."\">";
							echo "<option value = \"".$value['rowData']."\" disabled";
								if ((int)$value['depth'] > 0)
								{
									echo " "."style = \"margin-left : ".(15*(int)$value['depth'])."px;\"";
								}
							echo ">".$value['note'];
							echo "</option>";
						}
						else
						{
							if ((int)$value['depth'] == 0 && $end)
							{
								$end = false;
								echo "</optgroup>";
							}

							echo "<option value = \"".$value['rowData']."\"";
								if ((int)$value['depth'] > 0)
								{
									echo " "."style = \"margin-left : ".(15*(int)$value['depth'])."px;\"";
								}
							echo ">".$value['note'];
								if (!empty($value['p_adress']))
								{
									echo ", ".$value['p_adress'];
								}
								if (!empty($value['p_ort']))
								{
									echo ", ".$value['p_ort'];
								}
							echo "</option>";
						}
					}
				echo "</select>";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"banml";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">&nbsp;</label>";

			echo "<div class=\"col-sm-10\">";
				echo "<div class=\"form-check checkbox-slider--b\">";
					echo "<label>";
						echo "<input type=\"checkbox\" id = \"banml";
							if (!empty($rowData['tableKey']))
							{
								echo "[".$rowData['tableKey']."]";
							}
						echo "\" class = \"";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" ";
							if (!empty($rowData['banml']))
							{
								echo " "."checked";
							}
						echo "><span> ".$renderActityBody[16]."</span>";
					echo "</label>";
				echo "</div>";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"	minantal";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[9]."</label>";

			echo "<div class=\"col-sm-2\">";
				echo "<input type=\"number\" min = \"0\" class=\"form-control ";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" id=\"minantal";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\"";
				if (!empty($rowData['minantal']))
					{
						echo " "."value = \"".$rowData['minantal']."\"";
					}
				echo ">";
			echo "</div>";

		echo "</div>";

		echo "<div class=\"row mb-3\">";

			echo "<label for=\"maxantal";
				if (!empty($rowData['tableKey']))
				{
					echo "[".$rowData['tableKey']."]";
				}
			echo "\" class=\"col-sm-2 col-form-label\">".$renderActityBody[10]."</label>";

			echo "<div class=\"col-sm-2\">";
				echo "<input type=\"number\" min = \"0\" class=\"form-control ";
					if (!empty($rowData['tableKey']))
					{
						echo " "."syncData";
					}
				echo "\" data-replace_table = \"".$replaceTable[PREFIX.'activities']."\" id=\"maxantal";
					if (!empty($rowData['tableKey']))
					{
						echo "[".$rowData['tableKey']."]";
					}
				echo "\"";
				if (!empty($rowData['maxantal']))
					{
						echo " "."value = \"".$rowData['maxantal']."\"";
					}
				echo ">";
			echo "</div>";

		echo "</div>";
	
	if (empty($rowData['tableKey']))
	{
		echo "<div class=\"row mb-3\">";

			echo "<label for=\"button\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

			echo "<div class=\"col-sm-10\">";
				echo "<button class = \"btn btn-secondary form-control\" id = \"addActivity\" data-form = \"addActivityForm\">".$renderActityBody[18]."</button>";
			echo "</div>";

		echo "</div>";
		
		echo "</form>";
	}
}

function callCancelActivity()
{
	global $link;
	
	global $link;
	
	$langStrings = getLangstrings();
	$callCancelActivity = $langStrings['callCancelActivity'];
	
	$table = "`".PREFIX."activities`";
	$table1 = "`".PREFIX."registered`";

	$table10 = "`".PREFIX."user`";

	$members = getMembers(false);
	
	$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$_POST['cancelActivity']."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$aid = $row['aid'];
		$activity = $row['rubrik'];
	}
	
	$sql = "SELECT * FROM ".$table1." t1 INNER JOIN ".$table10." t10 ON t10.uid = t1.uid WHERE aid = '".$aid."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$uid[$row['uid']] = $row;
	}
	
	$sql = "UPDATE ".$table." SET canceld = 1 WHERE aid = '".$aid."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	$siteSetting = getSiteSettings();
	
	foreach ($uid as $key => $value)
	{
		$emailTemplate = renderEmailTemplate();
				
		$mailHeader = $callCancelActivity[3];
		
		$mail = $value['email'];
		
		$name = ucfirst($value['firstName'])." ".ucfirst($value['sureNme'])[0];

		$message = str_replace("~~activityHeader~~", $activity, $callCancelActivity[4]);

		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

		$headerMail =  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
		
		$mailHeader = $callCancelActivity[1];
		$mail = $value['mobile']."@".$siteSetting['domain_email2sms'];
		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		
		queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
	}
	
	include_once($path."tasks.php");
		//start_task(false);
}

function callReactivateActivity()
{
	global $link;
	
	global $link;
	
	$langStrings = getLangstrings();
	$callReactivateActivity = $langStrings['callReactivateActivity'];
	
	$table = "`".PREFIX."activities`";
	$table1 = "`".PREFIX."registered`";

	$table10 = "`".PREFIX."user`";

	$members = getMembers(false);
	
	$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$_POST['reactivateActivity']."'";
	echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$aid = $row['aid'];
		$activity = $row['rubrik'];
	}
	
	$sql = "SELECT * FROM ".$table1." t1 INNER JOIN ".$table10." t10 ON t10.uid = t1.uid WHERE aid = '".$aid."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$uid[$row['uid']] = $row;
	}
	
	$sql = "UPDATE ".$table." SET canceld = 0 WHERE aid = '".$aid."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	$siteSetting = getSiteSettings();
	
	foreach ($uid as $key => $value)
	{
		$emailTemplate = renderEmailTemplate();
				
		$mailHeader = $callReactivateActivity[3];
		
		$mail = $value['email'];
		
		$name = ucfirst($value['firstName'])." ".ucfirst($value['sureNme'])[0];

		$message = str_replace("~~activityHeader~~", $activity, $callReactivateActivity[4]);

		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

		$headerMail =  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
		
		$mailHeader = $callReactivateActivity[1];
		$message = str_replace("~~activityHeader~~", $activity, $callReactivateActivity[2]);
		
		$mail = $value['mobile']."@".$siteSetting['domain_email2sms'];
		
		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		
		queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
	}
	
	include_once($path."tasks.php");
		//start_task(false);
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME']))
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
	
	if (isset($_POST['cancelActivity']))
	{
		callCancelActivity();
		
		return false;
	}
	else if ($_POST['reactivateActivity'])
	{
		callReactivateActivity();
		return false;
	}
	
	
	echo "<div id = \"modalHeader\">";
		renderActivityHeader();
	echo "</div>";
	
	echo "<div id = \"modalBody\">";
		renderActityBody();
	echo "</div>";
	
	
	//renderActityBody();
}
?>