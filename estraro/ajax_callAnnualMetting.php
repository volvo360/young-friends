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
	
	if ($replaceTable[$_POST['replaceTable']] == PREFIX."annualMeeting_agenda")
	{
		ajax_annualMeeting_agenda();
	}
}

function ajax_annualMeeting_agenda()
{
	global $link;
	
	$langStrings = getLangstrings();
    $ajax_annualMeeting_agenda = $langStrings['ajax_annualMeeting_agenda'];
	
	$siteSetting = getSiteSettings();
	
	$table = "`".PREFIX."annualMeeting_agenda`";
	
	$table2 = "`".PREFIX."activities`";
	$table3 = "`".PREFIX."registered`";
	
	$sql = "UPDATE ".$table." SET locked = '1' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAnnualMetting'])."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	$sql = "SELECT * FROM ".$table."  WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceAnnualMetting'])."'";
	$result = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$sql = "SELECT MAX(aid)+1 as aid FROM ".$table2."";
		$result2 = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		
		while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			$aid = $row2['aid'];
		}
		
		$sql = "INSERT INTO ".$table2." (aid, rubrik, aktivitet, datum, tid, plats, ansvarig) VALUES ('".$aid."', '".$ajax_annualMeeting_agenda[3]."', '".mysqli_real_escape_string($link, $ajax_annualMeeting_agenda[4])."', '".$row['meetingDay']."', '".$row['time']."', '".$row['place']."', '".$row['responsible']."')";
		
		$result2 = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		
		checkTable($table2);
		
		$sql = "INSERT INTO ".$table3." (aid, uid, anmald) VALUES ('".$aid."', '".$row['responsible']."', NOW())"; 
		$result2 = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		
		checkTable($table3);
	}
	
	$table10 = "`".PREFIX."user`";
    
	$sql = "SELECT * FROM ".$table10." WHERE betalt > CURDATE() AND uid > 0 ORDER BY firstName, sureName";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $emailTemplate = renderEmailTemplate();
		
		$name = ucfirst($row['firstName'])." ".ucfirst($row['sureName']);
		
		$mail = $row['email'];
		
		$mobile = $row['mobile'];
				
		$mailHeader = $ajax_annualMeeting_agenda[1];

		$message = mysqli_real_escape_string($link, $ajax_annualMeeting_agenda[2]);
			
		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

		$headerMail =  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
		
		$mailHeader = $ajax_annualMeeting_agenda[5];
				
		$message = $ajax_annualMeeting_agenda[6];

		$mail = $mobile."@".$siteSetting['domain_email2sms'];;

		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
    }
	
	include_once($path."tasks.php");
	start_task(false);
}