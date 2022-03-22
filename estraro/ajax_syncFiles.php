<?php

session_start();

/*echo "<!DOCTYPE html>";
echo "<html lang=\"sv\">";
echo "<meta name=\"robots\" content=\"noindex\" />";*/

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
include_once($path."common/modal.php");

$replaceTable = getReplaceTable();

function countFiles()
{
	$files = array_diff(scandir("./files/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	return count($files);
}

function countFilesAnnualMeeting()
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
	
	$files = array_diff(scandir($path."annualmeeting/files/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	return count($files);
}	
	
if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
	if ($_POST['replace_annualmeeting'] || $_POST['replaceAgenda'])
	{
		listFilesAnnualMeeting();
		return false;
	}
	
	listFiles();
}

function listFiles()
{
	$files = array_diff(scandir("./files/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	if (count($files) > 0)
	{
		echo "<div id = \"tree_ajax_".$replaceTable[PREFIX."border_protocol"]."\" class =\"fancyTreeClass\" data-replace_table = \"".$replaceTable[PREFIX.'border_protocol']."\">";
			echo "<ul id = \"tree_ajax_".$replaceTable[PREFIX."border_protocol"]."-data\" style = \"display:none;\" >";
				foreach ($files as $key => $row)
				{
					echo "<li id = \"".$row."\" data-modal = \"1\" data-replace_agenda = \"".$_POST['replace_agenda']."\"";
					echo ">".$row."</li>";
				}
			echo "</ul>";
		echo "</div><br>";
	}
}

function listFilesAnnualMeeting()
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
	
	if ($_POST['replace_annualmeeting'])
	{
		$_POST['replace_agenda'] = $_POST['replace_annualmeeting'];
	}
	else if ($_POST['replaceAgenda'])
	{
		$_POST['replace_agenda'] = $_POST['replaceAgenda'];
	}	
	
	$files = array_diff(scandir($path."annualmeeting/files/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	if (count($files) > 0)
	{
		echo "<div id = \"tree_ajax_".$replaceTable[PREFIX."border_protocol"]."\" class =\"fancyTreeClass\" data-replace_table = \"".$replaceTable[PREFIX.'border_protocol']."\">";
			echo "<ul id = \"tree_ajax_".$replaceTable[PREFIX."border_protocol"]."-data\" style = \"display:none;\" >";
				foreach ($files as $key => $row)
				{
					echo "<li id = \"".$row."\" data-modal = \"1\" data-replace_agenda = \"".$_POST['replace_agenda']."\"";
					echo ">".$row."</li>";
				}
			echo "</ul>";
		echo "</div><br>";
	}
}	
?>