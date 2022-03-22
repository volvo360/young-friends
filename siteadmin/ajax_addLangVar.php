<?php
	session_start();
    error_reporting(E_ALL);
    
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

    $replaceTable = getReplaceTable(false);
        
    if (empty($_POST['replaceTable']))
    {
        return true;
    }

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

    echo __LINE__." ".$replaceTable[$_POST['replaceTable']]."<br>";

    if ($replaceTable[$_POST['replaceTable']] === PREFIX."lang")
	{
        sync_lang();
	}

    function sync_lang()
    {
        global $link;
        
        $table = "`".PREFIX."lang_var`";
        $table2 = "`".PREFIX."lang`";
        
        $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['replaceVar'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
		//if (mysqli_num_rows($result) == 0)
		{
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$variable = $row['variable'];
			}

			$sql = "SELECT CASE WHEN MAX(arrayKey) IS NULL THEN 1 ELSE MAX(arrayKey) +1 END AS arrayKey FROM ".$table2." WHERE variable = '".$variable."'";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$arrayKey = $row['arrayKey'];
			}

			$sql = "INSERT INTO ".$table2." (variable, arrayKey, note) VALUES ('".$variable."', '".$arrayKey."', '".mysqli_real_escape_string($link, $_POST['note'])."')";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		}
        
        checkTable($table2);
    }