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

include_once("showAjax.php");

function informAuditor($auditor = null)
{
	global $link;
	
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
	
	$langStrings = getLangstrings();
	$informAuditor = $langStrings['informAuditor'];
	
	$siteSetting = getSiteSettings();
	
	$table = "`".PREFIX."accounting`";
	$table2 = "`".PREFIX."accounting_year`";

	$table10 = "`".PREFIX."user`";
	$table20 = "`".PREFIX."mission`";
	$table21 = "`".PREFIX."roles`";

	$sql = "SELECT * FROM ".$table20." t20 INNER JOIN ".$table21." t21 ON t20.assignment_id = t21.assignment_id INNER JOIN ".$table10." t10 ON t10.autoId = t20.uid WHERE extra = 'cashier'";
	echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cashier = $row['firstName']." ".$row['sureName'];
	}
	
	foreach ($auditor as $key => $row)
	{
		$emailTemplate = renderEmailTemplate();
		
		$name = ucfirst($row['firstName'])." ".ucfirst($row['sureName']);
		
		$mail = $row['email'];
		
		$mobile = $row['mobile'];
				
		$mailHeader = $informAuditor[1];

		$message = str_replace("~~cashier~~", $cashier, $informAuditor[2]);
		
		$revisionURL = "<a href = ".$url."revision.php?account=".$_POST['replaceKey'].">".$url."revision.php?account=".$_POST['replaceKey']."</a>";
		
		$revisionButton = "<tr>";
			$revisionButton .= "<td style=\"padding: 0 20px 20px;\">";
				$revisionButton .= "<table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"margin: auto;\">";
					$revisionButton .= "<tr>";
						$revisionButton .= "<td class=\"button-td button-td-primary\" style=\"border-radius: 4px; background: #222222;\">";
							$revisionButton .= "<a class=\"button-a button-a-primary\" href=\"".$url."revision.php?account=".$_POST['replaceKey']."\" style=\"background: #222222; border: 1px solid #000000; font-family: sans-serif; font-size: 15px; line-height: 15px; text-decoration: none; padding: 13px 17px; color: #ffffff; display: block; border-radius: 4px;\">".$informAuditor[5]."</a>";
						$revisionButton .= "</td>";
					$revisionButton .= "</tr>";
				$revisionButton .= "</table>";
			$revisionButton .= "</td>";
		$revisionButton .= "</tr>";
		
		$message = str_replace("~~revisionURL~~", $revisionURL, $message);
		
		
		$message = mysqli_real_escape_string($link, $message);
			
		$emailTemplate = str_replace("~~button~~", $revisionButton, $emailTemplate);
		
		$emailTemplate = str_replace("~~messageMail~~", $message, $emailTemplate);

		$emailTemplate = str_replace("~~reciverName~~", "Hej ".$name."!", $emailTemplate);

		$emailTemplate = str_replace("~~previewMessage~~", substr($message, 0, 150), $emailTemplate);

		$headerMail =  "MIME-Version: 1.0" . "\r\n";
		$headerMail .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headerMail .= 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $emailTemplate, 1, null, $headerMail);
		
		$mailHeader = $informAuditor[3];
				
		$message = $informAuditor[4];

		$mail = $mobile."@".$siteSetting['domain_email2sms'];;

		$headerMail = 'From: Young Friends<info@young-friends.org>' . "\r\n";
		queMail(0, $mail, $mailHeader, $message, $message, null, 1, $headerMail);
	}
	
	include_once($path."tasks.php");
	//start_task(false);
}

$langStrings = getLangstrings();
$ajax_savePostAccounting = $langStrings['ajax_savePostAccounting'];

$table = "`".PREFIX."accounting`";
$table2 = "`".PREFIX."accounting_year`";

$table10 = "`".PREFIX."user`";
$table20 = "`".PREFIX."mission`";
$table21 = "`".PREFIX."roles`";

if ($_POST['closeFiscalYear'])
{
	$sql = "SELECT * FROM ".$table2." WHERE tableKey = '".$_POST['replaceKey']."'";
	echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result))
	{
		$accounting = $row['autoId'];
		$startBalance = (float)$row['startBalance'];
		$year = $row['year'];
	}
	
	$sql = "SELECT SUM(income) as income, SUM(expence) as expence FROM ".$table." WHERE accounting = '".$accounting."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result))
	{
		$income = (float)$row['income'];
		$expence = (float)$row['expence'];
	}
	
	$yearBalance = $startBalance + $income -$expence;
	
	$newYear = date("Y", strtotime($year+' +1 year'));
	
	$sql = "UPDATE ".$table2." SET status = 'locked'  WHERE tableKey = '".$_POST['replaceKey']."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	$sql = "INSERT INTO ".$table2." (year, status, startBalance) VALUES ('".$newYear."', 'active', '".$yearBalance."')";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	checkTable($table2);
	
	include_once("showAjax.php");
	
	$_POST['id'] = $_POST['replaceKey'];
	
	showAjax_accounting_year();
	
	return false;
}
else if ($_POST['revision'])
{
	$sql = "SELECT * FROM ".$table2." WHERE tableKey = '".$_POST['replaceKey']."'";
	echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result))
	{
		$accounting = $row['autoId'];
		$startBalance = (float)$row['startBalance'];
		$year = $row['year'];
	}
	
	$sql = "SELECT * FROM ".$table20." t20 INNER JOIN ".$table21." t21 ON t20.assignment_id = t21.assignment_id INNER JOIN ".$table10." t10 ON t10.autoId = t20.uid WHERE extra = 'auditor'";
	echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$auditor[] = $row;
	}
	
	informAuditor($auditor);
	
	$sql = "UPDATE ".$table2." SET status = 'locked', revisionBy = '-1'  WHERE tableKey = '".$_POST['replaceKey']."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	checkTable($table2);
	
	include_once("showAjax.php");
	
	$_POST['id'] = $_POST['replaceKey'];
	
	showAjax_accounting_year();
	
	return false;
}

$sql = "SELECT * FROM ".$table2." WHERE year = '".mysqli_real_escape_string($link, date("Y", strtotime($_POST['accountDate'])))."'";
$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$status = $row['status'];
	$accounting = $row['autoId']; 
}

if ($status == "active")
{
	foreach ($_POST as $key => $value)
	{
		if ($key == "accountType")
		{
			if ($value == "income")
			{
				if ($_POST['typeIncome'] == "memberFee")
				{
					$members = getMembers();
					$members2 = getMembers(false);

					include_once("ajax_syncPayment.php");
					
					foreach ($_POST['membershipFee'] as $sub_key => $sub_value)
					{
						$note = str_replace("~~member~~", $members[$sub_value], $ajax_savePostAccounting[1]);

						if (!empty($_POST['vertificateFiles']))
						{
							$vertificateFiles = "'".$_POST['vertificateFiles']."'";
						}
						else
						{
							$vertificateFiles = "NULL";
						}
						
						$sql = "INSERT INTO ".$table." (accounting, date, note, income, vertificateFiles) VALUES ('".$accounting."', '".mysqli_real_escape_string($link, $_POST['accountDate'])."', '".$note."', '".mysqli_real_escape_string($link, $_POST['amount'])."',". $vertificateFiles.")";
						$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
						
						ajax_syncPayment($sub_value);
					}
				}
				else if ($_POST['typeIncome'] == "other")
				{
					$note = mysqli_real_escape_string($link, $_POST['note']);

					if (!empty($_POST['vertificateFiles']))
					{
						$vertificateFiles = "'".$_POST['vertificateFiles']."'";
					}
					else
					{
						$vertificateFiles = "NULL";
					}
					
					$sql = "INSERT INTO ".$table." (accounting, date, note, income, vertificateFiles) VALUES ('".$accounting."', '".mysqli_real_escape_string($link, $_POST['accountDate'])."', '".$note."', '".mysqli_real_escape_string($link, $_POST['amount'])."', $vertificateFiles )";
					$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
				}
			}
			else if ($value == "expence")
			{
				if ($_POST['typeExpence'] == "domain")
				{
					$note = $ajax_savePostAccounting[2];
				}
				else if ($_POST['typeExpence'] == "hosting")
				{
					$note = $ajax_savePostAccounting[3];	
				}
				else if ($_POST['typeExpence'] == "sms")
				{
					$note = $ajax_savePostAccounting[4];
				}
				else if ($_POST['typeExpence'] == "annualmeeting")
				{
					$note = $ajax_savePostAccounting[2];
				}
				else if ($_POST['typeExpence'] == "other")
				{
					$note = mysqli_real_escape_string($link, $_POST['note']);
				}
				
				if (!empty($_POST['vertificateFiles']))
				{
					$vertificateFiles = "'".$_POST['vertificateFiles']."'";
				}
				else
				{
					$vertificateFiles = "NULL";
				}
				$sql = "INSERT INTO ".$table." (accounting, date, note, expence, vertificateFiles) VALUES ('".$accounting."', '".mysqli_real_escape_string($link, $_POST['accountDate'])."', '".$note."', '".mysqli_real_escape_string($link, $_POST['amount'])."', $vertificateFiles )";
				
				$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			}
			
			checkTable($table);
		}
		else
		{
			continue;
		}
		
		$table = "`".PREFIX."accounting_year`";
	
		checkTable($table);

		$sql = "SELECT node.* FROM ".$table." as node ORDER BY node.year DESC LIMIT 1;";
		
		
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$id = $row['tableKey'];
			$_POST['id'] = $id;
		echo __LINE__." ".$sql."<br>";
		
			showAjax_accounting_year();
		}

       
	}
}