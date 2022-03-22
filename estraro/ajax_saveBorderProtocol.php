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
include_once($path."common/send_mail.php");
include_once($path."common/emailTemplate.php");

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
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
	
	if ($replaceTable[$_POST['replaceTable']] == PREFIX."border_protocol")
	{
		ajax_border_protocol();
	}
}

function ajax_set_protocol_done()
{
	global $link;
	
	$siteSetting = getSiteSettings();
	
	$langStrings = getLangstrings();
    $ajax_set_protocol_done = $langStrings['ajax_set_protocol_done'];
	
	$table = "`".PREFIX."border_protocol`";
	$table2 = "`".PREFIX."border_agenda`";
	$table3 = "`".PREFIX."user`";
	
	$sql = "UPDATE ".$table." SET done = '1' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['protocolDone'])."'";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	$sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t2.autoId = t1.metingId INNER JOIN ".$table3." t3 ON t1.secretary = t3.uid WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['protocolDone'])."'";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$adjustment = $row['adjustment'];
		$date = $row['meetingDay'];
		$secretary = $row['firstName']." ".$row['sureName'];
	}
	
	$uid = array_map('tim', explode(",", $adjustment));
	
	if (is_array($uid))
	{
		$sql = "SELECT * FROM ".$table3." WHERE uid = '".implode("' OR uid ='", $uid)."'";
	}
	else
	{
		$sql = "SELECT * FROM ".$table3." WHERE uid = '".$adjustment."'";
	}
	
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$user[] = $row;
		
		$emailTemplate = renderEmailTemplate();
		
		$name = ucfirst($row['firstName'])." ".ucfirst($row['sureName'])[0];
				
		$mailHeader = $ajax_set_protocol_done[1];

		$message = str_replace("~~secretary~~", $secretary, $ajax_set_protocol_done[2]);
		
		$message = str_replace("~~borderDate~~", $date, $message);

		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

		$headerMail =  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $row['email'], $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
	
		$mailHeader = $ajax_set_protocol_done[3];

		$message = $ajax_set_protocol_done[4];

		$mail = $row['mobile']."@".$siteSetting['domain_email2sms'];;

		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
	}
}

function ajax_verifyProtocol()
{
	global $link;
	
	$table = "`".PREFIX."border_protocol`";
	
	$sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['verifyProtocol'])."'";
	echo __LINE__." ".$sql."<br>";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$adjustmentDone = $row['adjustmentDone'];
	}
	
	if (empty($adjustmentDone))
	{
		$adjustmentDone = $_SESSION['uid'];
	}
	else
	{
		$adjustmentDone .= ", ".$_SESSION['uid'];
	}
	
	$sql = "UPDATE ".$table." SET adjustmentDone ='".$adjustmentDone."', adjustmentDate = NOW() WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['verifyProtocol'])."'";
	echo __LINE__." ".$sql."<br>";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
}

function ajax_border_protocol()
{
	if (isset($_POST['protocolDone']))
	{
		ajax_set_protocol_done();
		return false;
	}
	else if (isset($_POST['verifyProtocol']))
	{
		ajax_verifyProtocol();
		return false;
	}
	
	global $link;
	
	$table = "`".PREFIX."border_protocol`";
	$table2 = "`".PREFIX."border_agenda`";
	
	$sql = "SELECT * FROM ".$table. " WHERE tableKey = '".key($_POST['meetingDay'])."'";
	//echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$metingId = $row['autoId'];
	}
	
	$members = getMembers(false);
	
	foreach ($_POST as $key => $value)
	{
		unset($temp);
		
		if ($key == "tableKey")
		{
			continue;
		}
		
		$sql = "SHOW COLUMNS FROM ".$table." LIKE '".$key."' ;";
		//echo __LINE__." ".$sql."<br>";
		$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

		if (mysqli_num_rows($result) > 0)
		{
			foreach ($value as $sub_key => $sub_value)
			if (is_array($sub_value))
			{	
				foreach ($sub_value as $sub3_key => $sub3_value)
				{
					if (array_key_exists($sub3_value, $members))
					{
						$temp[] = $members[$sub3_value];
					}
					else
					{
						$temp[] = $sub3_value;
					}
				}
				
				$sub_value2 = mysqli_real_escape_string($link, implode(",", $temp));
			}
			else
			{
				if (array_key_exists($sub_value, $members))
				{
					$sub_value2 = mysqli_real_escape_string($link, $members[$sub_value]);
				}
				else
				{
					$sub_value2 = mysqli_real_escape_string($link, $sub_value);
				}
				
			}
			
			$sql = "UPDATE ".$table." SET ".$key." = '".$sub_value2."' WHERE metingId = '".$metingId."'";
			//echo __LINE__." ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
		}
		else
		{
			$sql = "SHOW COLUMNS FROM ".$table2." LIKE '".$key."';";
			$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));

			if (mysqli_num_rows($result) > 0)
			{
				foreach ($value as $sub_key => $sub_value)
				if (is_array($sub_value))
				{
					$sub_value2 = mysqli_real_escape_string($link, implode(",", $sub_value));
				}
				else
				{
					$sub_value2 = mysqli_real_escape_string($link, $sub_value);
				}

				$sql = "UPDATE ".$table2." SET ".$key." = '".$sub_value2."' WHERE autoId = '".$metingId."'";
				$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
			}
		}
	}
}