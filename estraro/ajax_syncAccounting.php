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

include_once("showAjax.php");

$langStrings = getLangstrings();
$showAjax_accounting_year = $langStrings['showAjax_accounting_year'];

foreach ($_POST as $key => $value)
{
	if ($key === "accountType")
	{
		if ($value === "expence")
		{
			displayExpence();
		}
		else
		{
			displayIncome();
			
			echo " &nbsp;".$showAjax_accounting_year[5]." ";
			echo "<span id = \"subSpanIncome\">";
			displayMembers();
			echo "</span>";
			echo " ";
			echo "<input id = \"ammount\" type = \"number\" min = \"0\" step = \".01\" value = \"50\"> kr";
			
		}
	}
	else if ($key == "typeIncome")
	{
		if ($value === "memberFee")
		{
			displayMembers();
		}
		else
		{
			echo "<input type = \"text\" id = note>";
		}
	}
	else if ($key == "typeExpence")
	{
		if ($value === "other")
		{
			echo "<input type = \"text\" placeholder = \"notering\" id = note>";
		}
		else
		{
			
		}
	}
}