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
	$replaceTable = getReplaceTable(false);
	
	if ($replaceTable[$_POST['replaceTable']] == PREFIX."annualMeeting_protocol")
	{
		ajax_annualmeeting();
	}
}

function ajax_set_annualmeeting_done()
{
	global $link;
	
	$siteSetting = getSiteSettings();
	
	$langStrings = getLangstrings();
    $ajax_set_annualmeeting_done = $langStrings['ajax_set_annualmeeting_done'];
	
	$table = "`".PREFIX."annualMeeting_protocol`";
	$table2 = "`".PREFIX."annualMeeting_agenda`";
	$table3 = "`".PREFIX."user`";
	
	$sql = "UPDATE ".$table." SET done = '1' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['protocolDone'])."'";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	$sql = "SELECT *, t2.tableKey as agendaKey FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t2.autoId = t1.meetingId INNER JOIN ".$table3." t3 ON t1.secretary = t3.uid WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['protocolDone'])."'";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$adjustment = $row['adjustment'];
		$date = $row['meetingDay'];
		$secretary = $row['firstName']." ".$row['sureName'];
		
		$agendaKey = $row['agendaKey'];
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
				
		$mailHeader = $ajax_set_annualmeeting_done[1];

		$message = str_replace("~~secretary~~", $secretary, $ajax_set_annualmeeting_done[2]);
		
		$message = str_replace("~~borderDate~~", $date, $message);

		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

		$headerMail =  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $row['email'], $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
	
		$mailHeader = $ajax_set_annualmeeting_done[3];

		$message = $ajax_set_annualmeeting_done[4];

		$mail = $row['mobile']."@".$siteSetting['domain_email2sms'];;

		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
	}
	
	include_once("showAjax.php");
	$_POST['id'] = $agendaKey;
	showAjax_annualmeeting_protocol();
}

function ajax_verifyAnnualmeetingProtocol()
{
	global $link;
	
	$table = "`".PREFIX."annualMeeting_protocol`";
	$table2 = "`".PREFIX."annualMeeting_agenda`";
	
	$sql = "SELECT *, t1.tableKey as agendaKey FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t2.meetingId = t1.autoId  WHERE t2.tableKey = '".mysqli_real_escape_string($link, $_POST['verifyProtocol'])."'";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$adjustmentDone = $row['adjustmentDone'];
		
		$agendaKey = $row['agendaKey'];
		
		$adjustment = $row['adjustment'];
	}
	
	if (empty($adjustmentDone))
	{
		$adjustmentDone = $_SESSION['uid'];
	}
	else
	{
		$adjustmentDone .= ", ".$_SESSION['uid'];
	}
	
	$temp = array_map("trim", explode(",", $adjustment));
	
	if (!is_array($temp))
	{
		$temp[] = $adjustment;
	}
	$temp2 = array_map("trim", explode(",", $adjustmentDone));
	
	if (!is_array($temp2))
	{
		$temp2[] = $adjustmentDone;
	}
	
	if (count(array_diff($temp, $temp2)) === 0)
	{
		$done = 2;
	}
	else
	{
		$done = 1;
	}
	
	$sql = "UPDATE ".$table." SET adjustmentDone ='".$adjustmentDone."', adjustmentDate = NOW(), done = '".$done."' WHERE tableKey = '".$agendaKey."'";
	$result = mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	include_once("showAjax.php");
	$_POST['id'] = $_POST['verifyProtocol'];
	showAjax_annualmeeting_protocol();
	
}

function ajax_annualmeeting()
{
	if (isset($_POST['protocolDone']))
	{
		set_annualmeeting_done();
		return false;
	}
	else if (isset($_POST['verifyProtocol']))
	{
		ajax_verifyAnnualmeetingProtocol();
		return false;
	}
}