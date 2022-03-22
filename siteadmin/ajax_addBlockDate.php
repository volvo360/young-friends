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

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
	if ($_SERVER['SERVER_NAME'] === "server01")
	{
		$url = "//server01/flexshare/yf/";
		$path = "/var/flexshare/shares/yf/";
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

    if ($replaceTable[$_POST['replaceTable']] === PREFIX.'block_date')
    {
        ajax_add_block_date();
    }
}

function ajax_add_block_date()
{
	global $link;
	
	$table = "`".PREFIX."block_date`";
	
	$blockDate = mysqli_real_escape_string($link, $_POST['blockDate']);
	
	$note = mysqli_real_escape_string($link, $_POST['note']);
	
	if (empty($blockDate))
	{
		return false;
	}
		
	$sql = "SELECT * FROM ".$table." WHERE blockDate = '".$blockDate."'";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
	
	if (mysqli_num_rows($result) == 0)
	{
		$sql = "INSERT INTO ".$table." (blockDate, note) VALUES ('".$blockDate."', '".$note."')";
		$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
		
		checkTable($table);
	}
	
}