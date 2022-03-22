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

if (!isset($_SESSION['uid']))
{
	header('Location: '.$url);
}

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");
include_once($path."common/theme.php");
include_once($path."ajax_editActivity.php");

global $link;

$table = "`".PREFIX."activities`";
$table10 = "`".PREFIX."registered`";

$sql = "SHOW COLUMNS FROM ".$table;
$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$field[$row['Field']] = $row['Field'];
}
		
$members = getMembers(false);

foreach ($_POST as $key => $value)
{
	if (array_key_exists($key, $field))
	{
		$insertKey[] = mysqli_real_escape_string($link, $key);
		
		if (empty($value))
		{
			$insertData[] = "null";
		}
		else
		{
			if ($key == "ansvarig")
			{
				$insertData[] = "'".mysqli_real_escape_string($link, $members[$value])."'";
			}
			else
			{
				$insertData[] = "'".mysqli_real_escape_string($link, $value)."'";
			}
		}
	}
}

$sql = "INSERT INTO ".$table ."(".implode(", ", $insertKey).") VALUES (".implode(", ", $insertData).")";
$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

$insertId = mysqli_insert_id($link);

$sql = "SELECT MAX(aid)+1 as aid FROM ".$table;
$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

while ($row = mysqli_fetch_array($result))
{
	$aid = $row['aid'];
}

$sql = "UPDATE ".$table." SET aid = ".$aid." WHERE autoId = '".$insertId."'";
$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

echo __LINE__." ".$sql."<br>";

checkTable($table);

echo __LINE__." ".$sql."<br>";

if ($user = getUserData(mysqli_real_escape_string($link, $members[$value])))
{
    $sql = "INSERT INTO ".$table10." (aid, uid, anmald) VALUES (".$aid.", ".mysqli_real_escape_string($link, $members[$value]).", NOW())";
    echo __LINE__." ".$sql."<br>";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
}
					
					