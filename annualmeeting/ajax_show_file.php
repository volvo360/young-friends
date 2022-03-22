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

if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME']))
{
	$_SESSION['folder'] = "estraro/files/".$_POST['replaceAgenda']."/";
	
	echo "<div id = \"modalHeader\">";
	
	echo "</div>";
	
	echo "<div id = \"modalBody\">";
		echo "<iframe src=\"//docs.google.com/gview?url=".$url."estraro/files/".$_POST['replaceAgenda']."/source/".$_POST['id']."\" style = \"height : 60vh; width : 100%;\"></iframe>";
	echo "</div>";
}