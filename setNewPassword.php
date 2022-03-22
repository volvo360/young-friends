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
include_once($path."common/theme.php");
include_once($path."common/modal.php");

function func_setNewPassword()
{
	global $link;
	
	$langStrings = getLangstrings();
    $func_setNewPassword = $langStrings['func_setNewPassword'];
	
	$password = trim($_POST['password']);
	
	$repPassword = trim($_POST['repPassword']);
	
	$tableKey = $_POST['tableKey'];
	
	if (strlen($password) >= 6)
	{
		if ($password == $repPassword)
		{
			$password = password_hash($password, PASSWORD_BCRYPT);
			
			$table = "`".PREFIX."user`";
			$table10 = "`".PREFIX."reset_password`";

			$sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table10." t10 ON t1.uid = t10.uid WHERE t10.tableKey = '".$tableKey."'";
			//echo __LINE__." ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			$user = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			$sql = "UPDATE ".$table." SET password = '".$password."' WHERE uid = '".$user['uid']."'";
			//echo __LINE__." ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			$sql = "DELETE FROM ".$table10." WHERE tableKey = '".$tableKey."'";
			$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));
			
			$_POST['password'] = $repPassword;
			$_POST['mail'] = $user['email'];
			
			include_once("ajaxLogin.php");
			
			echo $func_setNewPassword[1]."<br>";
			return false;
		}
	}
}

function print_setNewPassword()
{
    global $link;
    
	if (isset($_POST['password']))
	{
		func_setNewPassword();
		return false;
	}
	
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
    
	$langStrings = getLangstrings();
    $print_setNewPassword = $langStrings['print_setNewPassword'];
    
	$tableKey = mysqli_real_escape_string($link,$_GET['tableKey']);
	
	$table = "`".PREFIX."user`";
	$table10 = "`".PREFIX."reset_password`";
	
	$sql = "SELECT * FROM ".$table." t1 INNER JOIN ".$table10." t10 ON t1.uid = t10.uid WHERE t10.tableKey = '".$tableKey."' AND validTo >= NOW()";
	//echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error: ".mysqli_error ($link));

	if (mysqli_num_rows($result) > 0)
	{
		$user = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo "<form id = \"setNewPasswordForm\" target = \"".$_SERVER['PHP_SELF']."\">";

			echo "<div class=\"row mb-3\">";
				echo "<img src = \"".$url."/img/orchids.jpg\" >";
			echo "</div>";

			echo "<h1>".$print_setNewPassword[4]." ".$user['firstName']." ".$user['sureName']."!</h1><br>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"password\" class=\"col-sm-2 col-form-label\">".$print_setNewPassword[1]."</label>";
				echo "<div class=\"col-sm-5\">";
					echo "<input type=\"password\" class=\"form-control\" id = \"password\" name = \"password\">";
				echo "</div>";
			echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"repPassword\" class=\"col-sm-2 col-form-label\">".$print_setNewPassword[2]."</label>";
				echo "<div class=\"col-sm-5\">";
					echo "<input type=\"password\" class=\"form-control\" id = \"repPassword\"  name = \"repPassword\">";
				echo "</div>";
			echo "</div>";

			echo "<div class=\"row mb-3\">";
				echo "<label for=\"password\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
				echo "<div class=\"col-sm-5\">";
					echo "<button type = \"button\" class=\"btn btn-info form-control\" name = \"setNewPassword\" id=\"setNewPassword\" value = \"".$tableKey."\">".$print_setNewPassword[3]."</button>";
				echo "</div>";
			echo "</div>";

		echo "</form>";
	}
	else
	{
		echo $print_setNewPassword[5]."<br>";
	}
}

if (basename(__FILE__) === basename($_SERVER['PHP_SELF']))
{
	if (isset($_POST['password']))
	{
		func_setNewPassword();
		return false;
	}
	
    printHeader();
    
    $langStrings = getLangstrings();
    $setNewPassword = $langStrings['setNewPassword'];
    
    echo "<main id=\"main\">";

        echo "<!-- ======= Breadcrumbs ======= -->";
    
        echo "<section class=\"breadcrumbs\">";
            echo "<div class=\"container\">";
                echo "<h2>".$setNewPassword[1]."</h2>";
            echo "</div>";
        echo "</section><!-- End Breadcrumbs -->";

        echo "<section class=\"inner-page\">";
            echo "<div class=\"container\" data-aos=\"fade-up\" style = \"max-height : 61vh; height : 61vh; overflow:auto;\">";
                echo "<p>";
                    print_setNewPassword();
                echo "</p>";
            echo "</div>";
        echo "</section>";

    echo "</main><!-- End #main -->";
    print_modal();
    printFooter();
	
}

?>