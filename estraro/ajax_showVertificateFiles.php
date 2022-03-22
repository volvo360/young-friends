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
	
	if (!isset($_SESSION['uid']))
    {
        header ("Location:".$url);
    }
        
    global $link;
     
    $table = "`".PREFIX."accounting`"; 
    $table2 = "`".PREFIX."accounting_year`";
        
    $sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t1.accounting = t2.autoId WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['tableKey'])."'";
    $result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
        
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $file = $row['tableKey']."/source/".$row['vertificateFiles'];
    }
        
    $langStrings = getLangstrings();

    $ajax_showVertificateFiles = $langStrings['ajax_showVertificateFiles'];

    echo "<div id = \"modalHeader\">";
        echo $ajax_showVertificateFiles[1];
    echo "</div>";

    echo "<div id = \"modalBody\">";
        echo "<iframe src=\"//docs.google.com/gview?url=https:".$url."estraro/accounting/files/".$file."\" style = \"height : 60vh; width : 100%;\"></iframe>";
    echo "</div>";

    echo "<div id = \"modalFooter\">";
        echo "<button type=\"button\" class=\"btn btn-secondary closeModal\" data-dismiss=\"modal\">".$ajax_showVertificateFiles[2]."</button>";
    echo "</div>";
?>