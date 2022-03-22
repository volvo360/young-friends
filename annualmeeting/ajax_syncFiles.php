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


$replaceTable = getReplaceTable();

function countFiles()
{
	$files = array_diff(scandir("./files/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	return count($files);
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
	listFiles();
}

function listFiles()
{
	$files = array_diff(scandir("./files/".$_POST['replace_agenda']."/source/"), array('..', '.'));
	
	if (count($files) > 0)
	{
		echo "<div id = \"tree_ajax_".$replaceTable[PREFIX."annualMeeting_protoco"]."\" class =\"fancyTreeClass\" data-replace_table = \"".$replaceTable[PREFIX.'annualMeeting_protoco']."\">";
			echo "<ul id = \"tree_ajax_".$replaceTable[PREFIX."annualMeeting_protoco"]."-data\" style = \"display:none;\" >";
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