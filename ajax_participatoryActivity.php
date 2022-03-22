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
	
	if ($replaceTable[$_POST['replaceTable']] == PREFIX."activities")
	{
		ajax_update_participant();
	}
}

function ajax_update_participant()
{
	global $link;
	
	$siteSetting = getSiteSettings();
	
	$langStrings = getLangstrings();
	$ajax_update_participant = $langStrings['ajax_update_participant'];
	
	$table = "`".PREFIX."activities`";
	$table1 = "`".PREFIX."registered`";

	$table10 = "`".PREFIX."user`";

	$members = getMembers(false);
	
	if (isset($_POST['addMember2activity']))
	{
		if (empty($_POST['outsiders']))
		{
			$sql = "SELECT * FROM ".$table." t INNER JOIN ".$table1." t1 ON t.aid = t1.aid WHERE t.tableKey = '".mysqli_real_escape_string($link, $_POST['activityKey'])."' AND uid = '".$members[mysqli_real_escape_string($link, $_POST['addMember2activity'])]."'";
		}
		else
		{	
			$sql = "SELECT * FROM ".$table." t INNER JOIN ".$table1." t1 ON t.aid = t1.aid WHERE t.tableKey = '".mysqli_real_escape_string($link, $_POST['activityKey'])."' AND uid = '".$members[mysqli_real_escape_string($link, $_POST['addMember2activity'])]."' AND outsiders = '".mysqli_real_escape_string($link, $_POST['outsiders'])."'";
		}
		$result= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));
		
		if (mysqli_num_rows($result) > 0)
		{
			return false;
		}
		else
		{
			$sql = "SELECT * FROM ".$table." t1 WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['activityKey'])."'";
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			while ($row = mysqli_fetch_array($result))
			{
				$aid = $row['aid'];
			}
			
			if (empty($_POST['outsiders']))
			{
				$sql = "INSERT INTO ".$table1." (aid, uid, anmald) VALUES ('".$aid."', '".$members[mysqli_real_escape_string($link, $_POST['addMember2activity'])]."', NOW())";
			}
			else
			{
				$sql = "INSERT INTO ".$table1." (aid, uid, anmald, outsiders) VALUES ('".$aid."', '".$members[mysqli_real_escape_string($link, $_POST['addMember2activity'])]."', NOW(),'". mysqli_real_escape_string($link, $_POST['outsiders'])."'";
			}
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		}
		
		$sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table10." t10 ON t1.ansvarig = t10.uid WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['activityKey'])."'";
		echo __LINE__." ".$sql."<br>";
		$result= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$aid = $row['aid'];
            
            $respId = $row['ansvarig'];

			$aemail = $row['aemail'];

			$asms = $row['asms'];

			$name = ucfirst($row['firstName'])." ".ucfirst($row['sureName'][0]);

			$mail = $row['email'];

			$mobile = $row['mobile'];

			$activity = $row['rubrik'];
		}
		
		$sql = "SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['addMember2activity'])."'";
		//echo __LINE__." ".$sql."<br>";
		$result= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$uid = $row['uid'];
			
			$memberFristName = $row['firstName'];
			$memberSureName = $row['sureName'];
			
			$meberName = $memberFristName." ".$memberSureName;
		}
		
		$where = "uid = '".$uid."' AND aid = '".$aid."'";
		
		if (!empty(trim($_POST['outsiders'])))
		{
			$where .= " AND outsiders = '".mysqli_real_escape_string($link, trim($_POST['outsiders']))."'";
		}
		
		$sql = "SELECT * FROM ".$table1." WHERE ".$where;
		echo __LINE__." ".$sql."<br>";
		$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		
		if (mysqli_num_rows($result) > 0)
		{
			/*$sql = "INSERT INTO $table1 (aid, uid, anmald";
				if (!empty(trim($_POST['outsiders'])))
				{
					$sql .=", outsiders";
				}
			$sql .=") VALUES ('".$aid."', '".$uid."', NOW()";
				if (!empty(trim($_POST['outsiders'])))
				{
					$sql .=", '".mysqli_real_escape_string($link, trim($_POST['outsiders']))."'";
				}
			$sql .=")";
			
			echo __LINE__." ".$sql."<br>";
			
			$result= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));*/
			
			checkTable($table1);
			
			$siteSetting = getSiteSettings();
			
			echo __LINE__." Hmmmmmmm.....<br>";
			
			if ((int)$aemail > 0)
			{
				$emailTemplate = renderEmailTemplate();
				
				$mailHeader = str_replace("~~activityHeader~~", $activity, $ajax_update_participant[1]);
				
				$message = str_replace("~~member~~", $meberName, $ajax_update_participant[2]);
				$message = str_replace("~~activityHeader~~", $activity, $message);
				
				$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);
				
				$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);
				
				$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);
				
				$headerMail =  "MIME-Version: 1.0" . "\r\n";
				$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

				// More headers
				$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
                
                if ($respId !== $_SESSION['uid'])
                {
				    queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
                }
			}
			
			if ((int)$asms > 0)
			{
				$mailHeader = $ajax_update_participant[3];
				
				$message = str_replace("~~member~~", $meberName, $ajax_update_participant[2]);
				$message = str_replace("~~activityHeader~~", $activity, $message);
				
                 $message = str_replace("<br>", "\n", $message);
                
				$mail = $mobile."@".$siteSetting['domain_email2sms'];;
				
				$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
                
                if ($respId !== $_SESSION['uid'])
                {
				    queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
                }
			}
		}
		
		include_once($path."tasks.php");
		start_task(false);
	}
	else if (isset($_POST['userKey']))
	{
		$sql = "SELECT * FROM ".$table1." t1 INNER JOIN ".$table." t ON t1.aid = t.aid INNER JOIN ".$table10." t10 ON t10.uid = t1.uid WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['userKey'])."'";
		$result= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if (!empty($row['outsiders']))
			{
				$meberName = $row['outsiders']." via ";
			}
			
			$memberFristName = $row['firstName'];
			$memberSureName = $row['sureName'];
			
			$meberName .= $memberFristName." ".$memberSureName;
			
			$aid = $row['aid'];
		}
		
		$sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table10." t10 ON t1.ansvarig = t10.uid WHERE t1.aid = '".$aid."'";
		echo __LINE__." ".$sql."<br>";
		$result= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$aid = $row['aid'];

			$aemail = $row['aemail'];

			$asms = $row['asms'];

			$name = ucfirst($row['firstName'])." ".ucfirst($row['sureName'][0]);

			$mail = $row['email'];

			$mobile = $row['mobile'];

			$activity = $row['rubrik'];
		}
		
		$sql = "DELETE FROM ".$table1." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['userKey'])."'";
		$result= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));
		
		if ((int)$aemail > 0)
		{
			$emailTemplate = renderEmailTemplate();

			$mailHeader = str_replace("~~activityHeader~~", $activity, $ajax_update_participant[4]);

			$message = str_replace("~~member~~", $meberName, $ajax_update_participant[5]);
			$message = str_replace("~~activityHeader~~", $activity, $message);

			$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

			$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

			$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

			$headerMail =  "MIME-Version: 1.0" . "\r\n";
			$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// More headers
			$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
            
            if ($respId !== $_SESSION['uid'])
            {
			     queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
            }
		}

		if ((int)$asms > 0)
		{
			$mailHeader = $ajax_update_participant[6];

			$message = str_replace("~~member~~", $meberName, $ajax_update_participant[5]);
			$message = str_replace("~~activityHeader~~", $activity, $message);

            $message = str_replace("<br>", "\n", $message);
            
			$mail = $mobile."@".$siteSetting['domain_email2sms'];

			$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
			
            if ($respId !== $_SESSION['uid'])
            {
			     queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
            }
			
		}
	}
	
	include_once($path."tasks.php");
	start_task(false);
}