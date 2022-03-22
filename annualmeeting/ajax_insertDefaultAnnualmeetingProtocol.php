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
		ajax_annualMeeting_protocol();
	}
}

function ajax_annualMeeting_protocol()
{
	global $link;
	
	$table = "`".PREFIX."permAnnualMeetingProtocol`";
	
	$table10 = "`".PREFIX."annualMeeting_protocol`";
	$table11 = "`".PREFIX."annualMeeting_agenda`";
	
	$sql = "SELECT * FROM ".$table11." WHERE tableKey = '".$_POST['replace_agenda']."'";
	$result = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$meetingId = $row['meetingId'];
	}
	
	$sql = "SELECT node.*, (COUNT(parent.lft) - 1) AS depth
				FROM ".$table." AS node,
						".$table." AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				GROUP BY node.lft
				ORDER BY node.lft;";
	$result = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$protocoll[] = "<li>".$row['note']."</li>";
	}
	
	$table10 = "`".PREFIX."roles`";
	$table11 = "`".PREFIX."mission`";
	
	$table20 = "`".PREFIX."user`";

	$sql = "SELECT * FROM ".$table20." WHERE betalt > CURDATE() AND uid > 0 ORDER BY firstName, sureName";
	$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

	while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
	{
		$user[$row2['uid']] = ucfirst($row2['firstName'])." ".ucfirst($row2['sureName']);
	}
	
	$user2 = getMembers(false);
	
	$sql = "SELECT * FROM ".$table10." t10 INNER JOIN ".$table11." t11 ON t10.assignment_id = t11.assignment_id WHERE extra IS NOT NULL";
	$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".mysqli_error ($link));

	while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
	{
		if ($row2['extra'] === "members")
		{
			$mission[$row2['extra']][] = $user[$row2['uid']];
		}
		else
		{
			$mission[$row2['extra']] = $row2['uid'];
		}
		
	}
	
	$note = "<ol>".implode("", $protocoll)."</ol>";
	
	$note = str_replace(array("~~chairman~~", "~~secretery~~", "~~charimanMeeeting~~", "~~secreterMeeeting~~", "~~adjuster~~", "~~cashier~~", "~~nominee~~", "~~members~~", "~~auditor~~"), array($user[$mission['chairman']], $user[$mission['secretery']],$user[$user2[$_POST['chairman']]],$user[$user2[$_POST['secretery']]], $user[$user2[$_POST['adjuster']]], $user[$mission['cashier']], $user[$mission['nominee']], implode(", ", $mission['members']), $user[$mission['auditor']]), $note);
	
	if (is_array($_POST['adjuster']))
	{
		foreach ($_POST['adjuster'] as $key => $value)
		{
			$adjusterTemp[] = $user[$value];
			$adjusterTemp2[] = $user2[$value];
		}
		$adjuster = implode(", ",$adjusterTemp);
		$adjuster2 = implode(", ",$adjusterTemp2);
		
	}
	else
	{
		$adjuster = $user[$_POST['adjuster']];
		$adjuster2 = $user2[$_POST['adjuster']];
	}
	
	$table10 = "`".PREFIX."annualMeeting_protocol`";
	$sql = "INSERT INTO ".$table10." (meetingId, note, secretary, adjustment) VALUES ('".$meetingId."', '".$note."','".$user2[$_POST['secretery']]."', '".$adjuster2."')";
	//echo $sql."<br>";
	$result2= mysqli_query($link, $sql) or die ("Error: ".__LINE__." ".$sql." ".mysqli_error ($link));
	
	include_once("showAjax.php");
	
	$_POST['id'] = $_POST['replace_agenda'];
	
	showAjax_annualmeeting_protocol();
}