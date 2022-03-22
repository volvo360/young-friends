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

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/send_mail.php");
include_once($path."common/emailTemplate.php");

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

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
	$replaceTable = getReplaceTable(false);
	
	if ($replaceTable[$_POST['replaceTable']] == PREFIX."user")
	{
		ajax_syncPayment();
	}
}

function ajax_syncPayment($uid = null)
{
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
	
	global $link;
	
	$langStrings = getLangstrings();
    $ajax_syncPayment = $langStrings['ajax_syncPayment'];
	$ajax_savePostAccounting = $langStrings['ajax_savePostAccounting'];	
	
	$table = "`".PREFIX."user`";
	$table2 = "`".PREFIX."accounting`";
	$table3 = "`".PREFIX."accounting_year`";
	
	$sql = "SELECT * FROM ".$table3." WHERE status = 'active'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$accountYearId = $row['autoId'];	
	}
	
	$members = getMembers(false);
	
	if ((int)$_POST['payment'] > 0)
	{
		$date = date('Y', strtotime('+1 year'))."-03-31";
	}
	else
	{
		$date = date('Y', strtotime('-1 year'))."-03-31";
	}
	
	if (empty($uid))
	{
		preg_match_all("/\[[^\]]*\]/", $_POST['id'], $matches);
		$uid = str_replace(array("[","]"),array('',''),$matches[0][0]);
	}
	
	$siteSettings = getSiteSettings();
	
	print_r($siteSettings);
	echo "<br>";
	
	$sql = "UPDATE ".$table." SET betalt = '".$date."' WHERE tableKey = '".$uid."'";
	echo __LINE__. " ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	if (mysqli_affected_rows($link) > 0)
	{	
		$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$uid."'";
		echo __LINE__. " ".$sql."<br>";
		$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$name = $row['firstName'] ." ".ucfirst($row['sureName'])[0];

			$fullName = $row['firstName'] ." ".ucfirst($row['sureName']);
			
			$mail = $row['email'];
		}
		
		if ((int)$_POST['payment'] > 0)
		{
			
			$note = str_replace("~~member~~", $fullName, $ajax_savePostAccounting[1]);
			
			$sql = "INSERT INTO ".$table2." (accounting, date, note, income) VALUES (".$accountYearId.", NOW(), '".$note."', '".$siteSettings['memberFee']."')";
			echo __LINE__. " ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			echo __LINE__." Hmmmmm...<br>";
			
			$emailTemplate = renderEmailTemplate();
			
			echo __LINE__." Hmmmmm...<br>";

			$mailHeader = $ajax_syncPayment[3];

			$message = $ajax_syncPayment[1];

			$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

			$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

			$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

			$headerMail =  "MIME-Version: 1.0" . "\r\n";
			$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// More headers
			$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";

			queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);

			include_once($path."tasks.php");

			//start_task(false);
		}
		else
		{
			$note = str_replace("~~member~~", $fullName, $ajax_savePostAccounting[6]);
			
			$sql = "INSERT INTO ".$table2." (accounting, date, note, expence) VALUES (".$accountYearId.", NOW(), '".$note."', '".$siteSettings['memberFee']."')";
			echo __LINE__. " ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			$emailTemplate = renderEmailTemplate();

			$mailHeader = $ajax_syncPayment[4];

			$message = $ajax_syncPayment[2];

			$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

			$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

			$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

			$headerMail =  "MIME-Version: 1.0" . "\r\n";
			$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// More headers
			$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";

			queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);

			include_once($path."tasks.php");

			//start_task(false);
		}
	}
}