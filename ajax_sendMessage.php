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

$members = getMembers(false);

global $membersData;

$table10 = "`".PREFIX."groups`";
$table11 = "`".PREFIX."roles`";
$table12 = "`".PREFIX."mission`";

if (!empty(trim($_POST['messageSMS'])))
{
	$table = "`".PREFIX."user`";
	
    $sql = "SELECT * FROM ".$table." WHERE betalt > CURDATE() ORDER BY firstName, sureName";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result))
    {
        $membersData[$row['autoId']] = $row;
	}
	
	$headerSMS = mysqli_real_escape_string($link, $_POST['headerSMS']);
	
	$messageSMS = mysqli_real_escape_string($link, $_POST['messageSMS']);
	
	$breaks = array("<br />","<br>","<br/>");  
    $messageSMS = str_ireplace($breaks, "\r\n", $messageSMS); 
	
	$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
	
	if (!empty($_POST['groupSMS']))
	{
		foreach ($_POST['groupSMS'] as $key => $value)
		{
			$tableKey = mysqli_real_escape_string($link, $key);
			
			$sql = "SELECT * FROM ".$table10." t10 INNER JOIN ".$table11." t11 ON t11.groupId = t10.groupId INNER JOIN ".$table12." t12 ON t12.assignment_id = t11.assignment_id WHERE t10.tableKey = '".$tableKey."'";
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 

			while ($row = mysqli_fetch_array($result))
			{
				$_POST['memberSMS'][$row['uid']] = $row['uid'];
			}
		}
	}
	
	foreach ($_POST['memberSMS'] as $key => $value)
	{
		$tableKey = mysqli_real_escape_string($link, $key);
		
		if (!empty($membersData[$members[$key]]['mobile']))
		{
			$mail = $membersData[$members[$key]]['mobile']."@pixie.se";
		}
		else
		{
			$mail = $membersData[$key]['mobile']."@pixie.se";
		}
		
		if (!empty($_POST['ownSenderSMS']))
		{
			queMail($_SESSION['uid'], $mail, $headerSMS, $messageSMS, $messageSMS, null, 1, $headerMail);
		}
		else
		{
			queMail(0, $mail, $headerSMS, $messageSMS, $messageSMS, null, 1, $headerMail);
	
		}
	}
}
if (!empty(trim($_POST['messageMail'])))
{
	$langStrings = getLangstrings();
	$ajax_sendMessage = $langStrings['ajax_sendMessage'];
	
	$table = "`".PREFIX."user`";

	if (empty($membersData))
	{
		$sql = "SELECT * FROM ".$table." WHERE betalt > CURDATE() ORDER BY firstName, sureName";
		$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 

		while ($row = mysqli_fetch_array($result))
		{
			$membersData[$row['autoId']] = $row;
		}
	}
	
	$headerMail = mysqli_real_escape_string($link, $_POST['headerMail']);

	$messageMail = mysqli_real_escape_string($link, $_POST['messageMail']);

	$header_Mail =  "MIME-Version: 1.0" . "\r\n";
	$header_Mail .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	
	echo __LINE__ ." ".$_POST['memberSenderEmail']."<br>";
	
	if (empty($_POST['memberSenderEmail']))
	{
		$header_Mail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
	}
	else
	{
		$header_Mail .= "From: ".$membersData[$_SESSION['uid']]['firstName']." ".$membersData[$_SESSION['uid']]['sureName']."<".$membersData[$_SESSION['uid']]['email'].">" . "\r\n";
	}
	
	$name = $membersData[$_SESSION['uid']]['firstName']." ".$membersData[$_SESSION['uid']]['sureName'];
	
	if (!empty($_POST['mailGroup']))
	{
		foreach ($_POST['mailGroup'] as $key => $value)
		{
			$tableKey = mysqli_real_escape_string($link, $key);
			
			$sql = "SELECT * FROM ".$table10." t10 INNER JOIN ".$table11." t11 ON t11.groupId = t10.groupId INNER JOIN ".$table12." t12 ON t12.assignment_id = t11.assignment_id WHERE t10.tableKey = '".$tableKey."'";
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$groupMember[$row['uid']] = $_POST['mailMember'][$row['uid']] = $row['uid'];
				$group[$tableKey] = $row['note']; 
			}
		}
		
		$temp = array_diff_key($_POST['mailMember'], $groupMember);
		
		if (count($temp) > 0)
		{
			foreach ($temp as $key => $value)
			{
				$group[] = $membersData[$members[$key]]['firstName']." ".$membersData[$members[$key]]['sureName'];;
			}
		}
	}

	foreach ($_POST['mailMember'] as $key => $value)
	{
		$tableKey = mysqli_real_escape_string($link, $key);
		
		if (empty($value))
		{
			continue;
		}
		
		if (!empty($membersData[$members[$key]]['email']))
		{
			$mail = $membersData[$members[$key]]['email'];
			$name2 = $membersData[$member[$key]]['firstName']." ".$membersData[$member[$key]]['sureName'];
		}
		else
		{
			continue;
		}
		/*else
		{
			$mail = $membersData[$key]['email'];	
			$name2 = $membersData[$key]['firstName']." ".$membersData[$key]['sureName'];
		}*/
		
		$emailTemplate = renderEmailTemplate();
		
		if (!empty($_POST['mailGroup']))
		{
			if (empty($_POST['memberSenderEmail']))
			{
				$messageMailDB = nl2br($ajax_sendMessage[4]);
				$messageMailDB = str_replace("~~groups~~", strtolower(implode(", ", $group)), $messageMailDB);
			}
			else
			{
				$messageMailDB = str_replace("~~senderName~~", $name, nl2br($ajax_sendMessage[3]));
				$messageMailDB = str_replace("~~groups~~", strtolower(implode(", ", $group)), $messageMailDB);
			}
		}
		else
		{
			if (!empty($_POST['memberSenderEmail']))
			{
				$messageMailDB = nl2br($ajax_sendMessage[2]);
			}
			else
			{
				$messageMailDB = str_replace("~~senderName~~", $name, nl2br($ajax_sendMessage[1]));
			}
		}
		
		$messageMailDB = str_replace("~~senderName~~", $name2, $messageMailDB);
		$messageMailDB = str_replace("~~senderMessage~~", $messageMail, $messageMailDB);
		
		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);
		
		$emailTemplate = str_replace("~~previewMessage~~", $messageMailDB, $emailTemplate);
		$emailTemplate = str_replace("~~messageMail~~", $messageMail, $emailTemplate);
		
		queMail($_SESSION['uid'], $mail, $headerMail, $messageMailDB, $emailTemplate, 1, null, $header_Mail);
	}
}

include_once($path."tasks.php");
start_task(false);

?>