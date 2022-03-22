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

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");

function syncScrore($tid)
{
	global $link;
	
	$table = "`".PREFIX."bangolfresultat`";
	$table2 = "`".PREFIX."bangolfscore`";
	
	$sql= "SELECT * FROM ".$table2." WHERE posistion >= '0'";
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result,  MYSQLI_ASSOC))
	{
		$point[$row['posistion']]= $row['point'];	
	}	
	
	$sql = "UPDATE ".$table." SET points = '".$point[0]."' WHERE aktivitet = '$tid'";
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	$sql= "SELECT * FROM ".$table." WHERE aktivitet = '$tid' ORDER BY resultat ASC";
	$result= mysqli_query($link, $sql) or die ('Error: '.mysqli_error ($link));
	
	//Variabel för att ha koll på det finns spelare som har slutat på samma poäng. Om två eller flera spelare har samma poäng då utgår de efterföljande poängen
	$last = 0;
	//Variabel för att hämta poängen till respektive plats. Poängen hämstas från en array därför börjar vi på noll till vinaren
	$place = 1;
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		echo __LINE__." ".$row['resultat']." ".(int)$last."<br>";
		
		if ((int)$row['resultat'] == (int)$last)
		{
			$place++;
		}
		else
		{
			$last = (int)$row['resultat'];
			$number = $point[$place];
			if (empty($number) && $number !== 0)
			{
				//Vi har inga fler poäng att dela ut
				break;	
			}
			$sql2= "UPDATE ".$table." SET points = '$number' WHERE aktivitet = '$tid' AND resultat = $last";
			$result2= mysqli_query($link, $sql2) or die ('Error: '.mysqli_error ($link));
			$place++;
		}
	}
}

function ajax_syncTorunamentResult()
{
	global $link;
	
	$holes = mysqli_real_escape_string($link, reset($_POST['holes']));
	
	$activity = mysqli_real_escape_string($link, key($_POST['holes']));
	
	$table = "`".PREFIX."activities`";
	$table2 = "`".PREFIX."bangolfresultat`";
	
	$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$activity."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$aid = $row['aid'];
		
		$year = date("Y", strtotime($row['datum']));
	}
	
	$members = getMembers(false);
	
	foreach ($_POST['player'] as $key => $value)
	{
		$temp = array_map("trim", explode("_",$key));
		
		$resultat = mysqli_real_escape_string($link, $value);
		
		if ($temp[0] == $activity)
		{
			$sql = "SELECT * FROM ".$table2." WHERE memberId = '".$members[$temp[1]]."' AND aktivitet = '".$aid."'"; 
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			if ((int)$resultat < $holes)
			{
				while ($row = mysqli_fetch_array($result))
				{
					$autoId = $row['autoId'];
				}
				
				$sql = "DELETE FROM ".$table2." WHERE autoId = '".$autoId."'";
				$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
				
				continue;
			}
			
			if (mysqli_num_rows($result) > 0)
			{
				while ($row = mysqli_fetch_array($result))
				{
					$autoId = $row['autoId'];
				}
				
				$sql = "UPDATE ".$table2." SET resultat = '".$resultat."' WHERE autoId = '".$autoId."'";
				$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			}
			else
			{
				$sql = "INSERT INTO ".$table2." (memberId, resultat, holes, aktivitet, year) VALUES ('".$members[$temp[1]]."', '".$resultat."', '".$holes."', '".$aid."', '".$year."')";
				$result = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			}
		}
	}
	
	checkTable($table2);
	
	syncScrore($aid);
}

function ajax_syncTorunamentParticipant()
{
	global $link;
	
	$memberKey = mysqli_real_escape_string($link, reset($_POST['addMember2tournament']));
	
	$activity = mysqli_real_escape_string($link, key($_POST['addMember2tournament']));
	
	$members = getMembers(false);
	
	$table = "`".PREFIX."activities`";
	
	$table2 = "`".PREFIX."registered`";
	
	$sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t2.aid = t1.aid WHERE t1.tableKey = '".$activity."' AND uid = '".$members[$memberKey]."'";
	
	echo __LINE__." ".$sql."<br>";
	$result = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	if (mysqli_num_rows($result) == 0)
	{
		$sql = "SELECT * FROM ".$table." t1 WHERE t1.tableKey = '".$activity."'";
		$result = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		
		while ($row = mysqli_fetch_array($result))
		{		
			$sql = "INSERT INTO ".$table2." (aid, uid, anmald) VALUES ('".$row['aid']."', '".$members[$memberKey]."', NOW())";
			echo __LINE__." ".$sql."<br>";
			$result2  = mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		}
	}
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME']))
{
	$replaceTable = getReplaceTable(false);
	
	echo __LINE__." ".$replaceTable[$_POST['replaceTable']]."<br>";
	
	if ($replaceTable[$_POST['replaceTable']] === PREFIX."bangolfresultat")
	{
		ajax_syncTorunamentResult();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."activities")
	{
		echo __LINE__." ".$replaceTable[$_POST['replaceTable']]."<br>";
		ajax_syncTorunamentParticipant();
	}
}
?>