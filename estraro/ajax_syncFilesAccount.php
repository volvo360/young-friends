<?php

session_start();

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
include_once($path."common/theme.php");
include_once($path."common/modal.php");

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

$replaceTable = getReplaceTable();

function countFiles()
{
	$files = array_diff(scandir("./accounting/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	return count($files);
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
	listFiles();
}

function listFiles()
{
	global $link;
	
	$table = "`".PREFIX."accounting_year`";
	$table2 = "`".PREFIX."accounting`";
	
	$sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t2.accounting = t1.autoId WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['replace_agenda'])."' AND vertificateFiles IS NOT NULL";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result))
	{
		$existingFiles[] = $row['file'];
	}
	
	$files = array_diff(scandir("./accounting/files/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	if (is_array($existingFiles))
	{
		$files = array_diff($files, $existingFiles);	
	}
	
	if (count($files) > 0)
	{
		echo "<div id = \"tree_ajax_".$replaceTable[PREFIX."accounting_year"]."\" class =\"fancyTreeClass\" data-replace_table = \"".$replaceTable[PREFIX.'accounting_year']."\">";
			echo "<ul id = \"tree_ajax_".$replaceTable[PREFIX."accounting_year"]."-data\" style = \"display:none;\" >";
				foreach ($files as $key => $row)
				{
					echo "<li id = \"".$row."\" data-modal = \"1\" data-replace_agenda = \"".$_POST['replace_agenda']."\"";
					echo ">".$row."</li>";
				}
			echo "</ul>";
		echo "</div><br>";
		echo "<input type = \"hidden\" id = \"vertificateFiles\" value = \"".implode(", ", $files)."\">";
	}
}

?>