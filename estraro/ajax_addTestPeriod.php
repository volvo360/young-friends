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
		ajax_addTestperiod();
	}
}

function ajax_addTestperiod()
{
	global $link;
	
	$langStrings = getLangstrings();
    $ajax_addTestperiod = $langStrings['ajax_addTestperiod'];
	
	$siteSetting = getSiteSettings();
	
	$table = "`".PREFIX."user`";
	
	$sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceTestperiod'])."'";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$testmember = $row['testmember'];
		
		if (empty($testmember) || $testmember == '0000-00-00')
		{
			$testmember = date("Y-m-d", strtotime('+14 day'));
			$message = mysqli_real_escape_string($link, $ajax_addTestperiod[2]);
		}
		else
		{
			if ($testmember >= date("Y-m-d", strtotime($testmember.' -3 day')))
			{
				$testmember = date("Y-m-d", strtotime($testmember.' +14 day'));
				
				$message = mysqli_real_escape_string($link, $ajax_addTestperiod[5]);
				$renew = 1;
			}
		}
		
		$message = str_replace("~~testPeriod~~", $testmember, $message);		
		
		$sql = "UPDATE ".$table." SET testmember = '".$testmember."' WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceTestperiod'])."'";
		//echo __LINE__." ".$sql."<br>";
		$result2 = mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
		
        $emailTemplate = renderEmailTemplate();
		
		$name = ucfirst($row['firstName'])." ".ucfirst($row['sureName']);
		
		$mail = $row['email'];
		
		$mobile = $row['mobile'];
				
		$mailHeader = $ajax_addTestperiod[1];

		
			
		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

		$headerMail =  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
		
		$mailHeader = $ajax_addTestperiod[3];
		
		if (empty($renew))
		{
			$message = $ajax_addTestperiod[4];
		}
		else
		{
			$message = $ajax_addTestperiod[6];
			//$message = str_replace("~~testPeriod~~", $testmember, $message);
		}
		
		$mail = $mobile."@".$siteSetting['domain_email2sms'];;

		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
    }
	
	include_once("adminCadastre.php");
	
	rerenderNewAccount();
	
	include_once($path."tasks.php");
	//start_task(false);
}